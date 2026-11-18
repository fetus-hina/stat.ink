<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands\stat\actions;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Throwable;
use Yii;
use app\components\helpers\Battle3Helper;
use app\components\helpers\TypeHelper;
use app\components\helpers\db\Now;
use app\models\Season3;
use app\models\SplatoonVersion3;
use app\models\StatMatchingRangeDiff3;
use app\models\StatMatchingRangeSpread3;
use app\models\StatMatchingRangeSpreadDiff3;
use app\models\StatMatchingRangeTeam3;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function fprintf;
use function fwrite;
use function implode;
use function min;
use function sprintf;
use function strtotime;
use function vsprintf;

use const SORT_ASC;
use const STDERR;

/**
 * Updates the Matching Range (MR) stats for X Battle and Anarchy Battle (Series).
 */
final class MatchingRange3Action extends Action
{
    /**
     * The first version that uses the MR-based team division for each lobby
     */
    private const MIN_VERSIONS = [
        'xmatch' => '11.1.0',
        'bankara_challenge' => '12.0.0',
    ];

    /**
     * Battles in X Battle are classified by the uploader's X Power with this threshold
     */
    public const X_POWER_THRESHOLD = 2000;

    private const TMP_TABLE_NAME = '{{tmp_matching_range_battle3}}';

    public function run(): int
    {
        $db = TypeHelper::instanceOf(Yii::$app->db, Connection::class);
        $isOk = $db->transaction(
            $this->makeStats(...),
            Transaction::REPEATABLE_READ,
        );
        if (!$isOk) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->vacuumTables($db);

        return ExitCode::OK;
    }

    private function makeStats(Connection $db): bool
    {
        try {
            fwrite(STDERR, "Updating Matching Range stats\n");

            $minReleases = $this->getMinReleases($db);
            if (!$minReleases) {
                fwrite(STDERR, "No target versions\n");
                return true;
            }

            $seasons = $this->getTargetSeasons($db, min($minReleases));
            if (!$seasons) {
                fwrite(STDERR, "No target seasons\n");
                return true;
            }

            fprintf(
                STDERR,
                "Target seasons: %s\n",
                implode(', ', ArrayHelper::getColumn($seasons, 'key')),
            );

            $seasonIds = array_values(ArrayHelper::getColumn($seasons, 'id'));
            $this->createTmpTable($db, $seasonIds, $minReleases);

            $now = new Now();
            foreach ($this->getTargetTables() as $tableName => $selectFactory) {
                fprintf(STDERR, "Updating %s...\n", $tableName);
                $db->createCommand()
                    ->delete($tableName, ['season_id' => $seasonIds])
                    ->execute();

                $select = $selectFactory($now);
                $db->createCommand(
                    vsprintf('INSERT INTO %s (%s) %s', [
                        $db->quoteTableName($tableName),
                        implode(', ', array_map(
                            $db->quoteColumnName(...),
                            array_keys((array)$select->select),
                        )),
                        $select->createCommand($db)->rawSql,
                    ]),
                )->execute();
            }

            fwrite(STDERR, "OK\n");

            return true;
        } catch (Throwable $e) {
            fprintf(STDERR, "Error: %s\n", $e->getMessage());
            TypeHelper::instanceOf($db->transaction, Transaction::class)->rollBack();
            return false;
        }
    }

    /**
     * @return array<string, int> lobby key => release timestamp of the minimum version
     */
    private function getMinReleases(Connection $db): array
    {
        $versions = ArrayHelper::map(
            SplatoonVersion3::find()
                ->andWhere(['tag' => array_values(self::MIN_VERSIONS)])
                ->all($db),
            'tag',
            fn (SplatoonVersion3 $v): int => TypeHelper::int(strtotime($v->release_at)),
        );

        return array_filter(
            array_map(
                fn (string $tag): ?int => $versions[$tag] ?? null,
                self::MIN_VERSIONS,
            ),
            fn (?int $v): bool => $v !== null,
        );
    }

    /**
     * Seasons overlapping the recalculation period, and seasons that have no data yet
     *
     * @return Season3[]
     */
    private function getTargetSeasons(Connection $db, int $minRelease): array
    {
        $startDay = new DateTimeImmutable()
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->modify('midnight')
            ->modify(sprintf('%d days ago', Battle3Helper::ENTIRE_STATS_RECALC_DAYS));

        $now = new DateTimeImmutable()
            ->setTimezone(new DateTimeZone('Etc/UTC'));

        $minReleaseAt = new DateTimeImmutable()
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp($minRelease);

        return Season3::find()
            ->andWhere(['>', 'end_at', $minReleaseAt->format(DateTimeInterface::ATOM)])
            ->andWhere(['<=', 'start_at', $now->format(DateTimeInterface::ATOM)])
            ->andWhere(['or',
                ['&&', 'term',
                    new Expression(
                        vsprintf("TSTZRANGE(%s, %s, '[]')", [
                            $db->quoteValue($startDay->format(DateTimeInterface::ATOM)),
                            $db->quoteValue($now->format(DateTimeInterface::ATOM)),
                        ]),
                    ),
                ],
                ['not exists',
                    new Query()
                        ->select(['x' => new Expression('1')])
                        ->from(StatMatchingRangeTeam3::tableName())
                        ->andWhere('{{%stat_matching_range_team3}}.[[season_id]] = {{%season3}}.[[id]]'),
                ],
            ])
            ->orderBy(['start_at' => SORT_ASC])
            ->all($db);
    }

    /**
     * Creates a temporary table which has one row per battle
     *
     * @param int[] $seasonIds
     * @param non-empty-array<string, int> $minReleases
     */
    private function createTmpTable(Connection $db, array $seasonIds, array $minReleases): void
    {
        $ts = fn (int $t): string => $db->quoteValue(
            new DateTimeImmutable()
                ->setTimezone(new DateTimeZone('Etc/UTC'))
                ->setTimestamp($t)
                ->format(DateTimeInterface::ATOM),
        );

        $lobbyConds = [];
        foreach ($minReleases as $lobbyKey => $releaseAt) {
            $lobbyConds[] = vsprintf('({{%%lobby3}}.[[key]] = %s AND {{%%splatoon_version3}}.[[release_at]] >= %s%s)', [
                $db->quoteValue($lobbyKey),
                $ts($releaseAt),
                $lobbyKey === 'xmatch'
                    ? ' AND COALESCE({{%battle3}}.[[x_power_before]], {{%battle3}}.[[x_power_after]]) IS NOT NULL'
                    : '',
            ]);
        }

        $sql = vsprintf(
            <<<'SQL'
            CREATE TEMPORARY TABLE %1$s ON COMMIT DROP AS
            WITH target_battle AS (
              SELECT
                {{%%battle3}}.[[id]],
                {{%%season3}}.[[id]] AS [[season_id]],
                {{%%battle3}}.[[lobby_id]],
                CASE
                  WHEN {{%%lobby3}}.[[key]] = 'xmatch'
                    AND COALESCE({{%%battle3}}.[[x_power_before]], {{%%battle3}}.[[x_power_after]]) >= %2$d
                    THEN %2$d
                  ELSE 0
                END AS [[x_power_class]],
                {{%%battle3}}.[[rule_id]],
                {{%%result3}}.[[is_win]]
              FROM {{%%battle3}}
              INNER JOIN {{%%season3}} ON {{%%battle3}}.[[start_at]] <@ {{%%season3}}.[[term]]
              INNER JOIN {{%%lobby3}} ON {{%%battle3}}.[[lobby_id]] = {{%%lobby3}}.[[id]]
              INNER JOIN {{%%rule3}} ON {{%%battle3}}.[[rule_id]] = {{%%rule3}}.[[id]]
              INNER JOIN {{%%rule_group3}} ON {{%%rule3}}.[[group_id]] = {{%%rule_group3}}.[[id]]
              INNER JOIN {{%%result3}} ON {{%%battle3}}.[[result_id]] = {{%%result3}}.[[id]]
              INNER JOIN {{%%splatoon_version3}} ON {{%%battle3}}.[[version_id]] = {{%%splatoon_version3}}.[[id]]
              WHERE {{%%battle3}}.[[has_disconnect]] = FALSE
                AND {{%%battle3}}.[[is_automated]] = TRUE
                AND {{%%battle3}}.[[is_deleted]] = FALSE
                AND {{%%battle3}}.[[use_for_entire]] = TRUE
                AND {{%%battle3}}.[[start_at]] >= %3$s
                AND {{%%battle3}}.[[end_at]] IS NOT NULL
                AND {{%%battle3}}.[[start_at]] < {{%%battle3}}.[[end_at]]
                AND {{%%season3}}.[[id]] IN (%4$s)
                AND {{%%rule_group3}}.[[key]] = 'gachi'
                AND {{%%result3}}.[[key]] IN ('win', 'lose')
                AND (%5$s)
            ),
            team AS (
              SELECT
                {{%%battle_player3}}.[[battle_id]],
                {{%%battle_player3}}.[[is_our_team]],
                ROUND(AVG({{%%mainweapon3}}.[[matching_range]]), 3) AS [[mr]],
                MAX({{%%mainweapon3}}.[[matching_range]]) - MIN({{%%mainweapon3}}.[[matching_range]]) AS [[spread]]
              FROM target_battle
              INNER JOIN {{%%battle_player3}} ON target_battle.[[id]] = {{%%battle_player3}}.[[battle_id]]
              LEFT JOIN {{%%weapon3}} ON {{%%battle_player3}}.[[weapon_id]] = {{%%weapon3}}.[[id]]
              LEFT JOIN {{%%mainweapon3}} ON {{%%weapon3}}.[[mainweapon_id]] = {{%%mainweapon3}}.[[id]]
              GROUP BY {{%%battle_player3}}.[[battle_id]], {{%%battle_player3}}.[[is_our_team]]
              HAVING COUNT(*) = 4
                AND COUNT({{%%mainweapon3}}.[[matching_range]]) = 4
            )
            SELECT
              target_battle.[[season_id]],
              target_battle.[[lobby_id]],
              target_battle.[[x_power_class]],
              target_battle.[[rule_id]],
              target_battle.[[is_win]],
              our_team.[[mr]] AS [[our_mr]],
              their_team.[[mr]] AS [[their_mr]],
              our_team.[[spread]] AS [[our_spread]],
              their_team.[[spread]] AS [[their_spread]]
            FROM target_battle
            INNER JOIN team our_team
              ON target_battle.[[id]] = our_team.[[battle_id]] AND our_team.[[is_our_team]] = TRUE
            INNER JOIN team their_team
              ON target_battle.[[id]] = their_team.[[battle_id]] AND their_team.[[is_our_team]] = FALSE
            SQL,
            [
                $db->quoteTableName(self::TMP_TABLE_NAME),
                self::X_POWER_THRESHOLD,
                $ts(min($minReleases)),
                implode(', ', array_map(fn (int $id): string => (string)$id, $seasonIds)),
                implode(' OR ', $lobbyConds),
            ],
        );

        fwrite(STDERR, "Creating temporary table...\n");
        $count = $db->createCommand($sql)->execute();
        fprintf(STDERR, "%d battles\n", $count);

        $db->createCommand(sprintf('ANALYZE %s', $db->quoteTableName(self::TMP_TABLE_NAME)))
            ->execute();
    }

    /**
     * @return array<string, callable(Now): Query>
     */
    private function getTargetTables(): array
    {
        $keys = [
            'season_id' => '{{t}}.[[season_id]]',
            'lobby_id' => '{{t}}.[[lobby_id]]',
            'x_power_class' => '{{t}}.[[x_power_class]]',
            'rule_id' => '{{t}}.[[rule_id]]',
        ];

        // both teams of each battle as separate rows
        $teams = fn (string $column): Query => new Query()
            ->select([...$keys, 'value' => "{{t}}.[[our_{$column}]]"])
            ->from(['t' => self::TMP_TABLE_NAME])
            ->union(
                new Query()
                    ->select([...$keys, 'value' => "{{t}}.[[their_{$column}]]"])
                    ->from(['t' => self::TMP_TABLE_NAME]),
                true,
            );

        $teamDistrib = fn (string $column, string $valueColumn): callable => fn (Now $now): Query => new Query()
            ->select([
                ...$keys,
                $valueColumn => '{{t}}.[[value]]',
                'teams' => 'COUNT(*)',
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->from(['t' => $teams($column)])
            ->groupBy([...array_values($keys), '{{t}}.[[value]]']);

        $battleDiff = fn (string $column, string $valueColumn, string $winsColumn): callable => fn (Now $now): Query => new Query()
            ->select([
                ...$keys,
                $valueColumn => "ABS({{t}}.[[our_{$column}]] - {{t}}.[[their_{$column}]])",
                'battles' => 'COUNT(*)',
                // NULL for no difference (no "higher" team)
                $winsColumn => vsprintf('CASE WHEN %1$s = 0 THEN NULL ELSE SUM(CASE WHEN %2$s THEN 1 ELSE 0 END) END', [
                    "ABS({{t}}.[[our_{$column}]] - {{t}}.[[their_{$column}]])",
                    "{{t}}.[[is_win]] = ({{t}}.[[our_{$column}]] > {{t}}.[[their_{$column}]])",
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->from(['t' => self::TMP_TABLE_NAME])
            ->groupBy([
                ...array_values($keys),
                "ABS({{t}}.[[our_{$column}]] - {{t}}.[[their_{$column}]])",
            ]);

        return [
            StatMatchingRangeTeam3::tableName() => $teamDistrib('mr', 'mr'),
            StatMatchingRangeSpread3::tableName() => $teamDistrib('spread', 'spread'),
            StatMatchingRangeDiff3::tableName() => $battleDiff('mr', 'mr_diff', 'higher_wins'),
            StatMatchingRangeSpreadDiff3::tableName() => $battleDiff('spread', 'spread_diff', 'wider_wins'),
        ];
    }

    private function vacuumTables(Connection $db): void
    {
        $tables = [
            StatMatchingRangeTeam3::tableName(),
            StatMatchingRangeSpread3::tableName(),
            StatMatchingRangeDiff3::tableName(),
            StatMatchingRangeSpreadDiff3::tableName(),
        ];

        foreach ($tables as $table) {
            fprintf(STDERR, "Vacuuming %s\n", $table);
            $db->createCommand(
                vsprintf('VACUUM ( ANALYZE ) %s', [
                    $db->quoteTableName($table),
                ]),
            )->execute();
        }

        fwrite(STDERR, "OK\n");
    }
}
