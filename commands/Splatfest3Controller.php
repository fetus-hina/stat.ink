<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use LogicException;
use Yii;
use app\models\Language;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Splatfest3;
use app\models\Splatfest3StatsPower;
use app\models\Splatfest3StatsPowerHistogram;
use app\models\Splatfest3StatsWeapon;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_keys;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_unique;
use function array_values;
use function gmdate;
use function implode;
use function sprintf;
use function str_starts_with;
use function vsprintf;

use const DATE_ATOM;
use const SORT_ASC;

final class Splatfest3Controller extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate(bool $force = false): int
    {
        return $this->actionUpdateStats($force);
    }

    public function actionUpdateStats(bool $force = false): int
    {
        $status = Yii::$app->db->transaction(
            function (Connection $db) use ($force): int {
                $fests = $this->getUpdateTargetFests($db, $force);
                if (!$fests) {
                    $this->stderr("No target fests\n");
                    return ExitCode::OK;
                }

                return $this->updatePowerStats($db, $fests) && $this->updateWeaponStats($db, $fests)
                    ? ExitCode::OK
                    : ExitCode::UNSPECIFIED_ERROR;
            },
            Transaction::REPEATABLE_READ,
        );

        if ($status === ExitCode::OK) {
            if (!$this->vacuumStatsTables(Yii::$app->db)) {
                $status = ExitCode::UNSPECIFIED_ERROR;
            }
        }

        return $status;
    }

    /**
     * @return Splatfest3[]
     */
    private function getUpdateTargetFests(Connection $db, bool $force): array
    {
        return Splatfest3::find()
            ->orderBy(['id' => SORT_ASC])
            ->andWhere(['and',
                $force
                    ? '1 = 1'
                    : ['>=', 'end_at', gmdate(DATE_ATOM, $_SERVER['REQUEST_TIME'] - 2 * 86400)],
            ])
            ->all($db);
    }

    /**
     * @param Splatfest3[] $targetFests
     */
    private function updatePowerStats(Connection $db, array $targetFests): bool
    {
        return array_reduce(
            array: array_map(
                function (Splatfest3 $fest) use ($db): bool {
                    $isGlobalFest = str_starts_with($fest->key, 'JUEA-');
                    return $this->updatePowerStatsAbstract($db, $fest, $isGlobalFest) &&
                        $this->updatePowerStatsHistogram($db, $fest, $isGlobalFest);
                },
                $targetFests,
            ),
            callback: fn (bool $carry, bool $item): bool => $carry && $item,
            initial: true,
        );
    }

    private function updatePowerStatsAbstract(
        Connection $db,
        Splatfest3 $targetFest,
        bool $isGlobalFest,
    ): bool {
        $this->stderr('Updating splatfest3_stats_power for ' . $targetFest->name . "...\n");
        $lobby = Lobby3::find()
            ->andWhere(['key' => 'splatfest_challenge'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        $rule = Rule3::find()
            ->andWhere(['key' => 'nawabari'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        if (!$lobby || !$rule) {
            throw new LogicException('Lobby or Rule not found');
        }

        $aggBattles = 'SUM(CASE WHEN {{%battle3}}.[[fest_power]] IS NOT NULL THEN 1 ELSE 0 END)';
        $query = (new Query())
            ->select([
                'splatfest_id' => '{{%splatfest3}}.[[id]]',
                'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
                'battles' => 'COUNT(*)',
                'agg_battles' => $aggBattles,
                'average' => 'AVG({{%battle3}}.[[fest_power]])',
                'stddev' => 'STDDEV_SAMP({{%battle3}}.[[fest_power]])',
                'minimum' => 'MIN({{%battle3}}.[[fest_power]])',
                'p05' => 'PERCENTILE_DISC(0.05) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p25' => 'PERCENTILE_DISC(0.25) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p50' => 'PERCENTILE_DISC(0.50) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p75' => 'PERCENTILE_DISC(0.75) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p80' => 'PERCENTILE_DISC(0.80) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p95' => 'PERCENTILE_DISC(0.95) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'maximum' => 'MAX({{%battle3}}.[[fest_power]])',
                'histogram_width' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                    $aggBattles,
                    'STDDEV_SAMP({{%battle3}}.[[fest_power]])',
                ]),
                'last_posted_at' => 'MAX({{%battle3}}.[[created_at]])',
            ])
            ->from('{{%battle3}}')
            ->innerJoin(
                '{{%splatfest3}}',
                implode(' AND ', [
                    "{{%battle3}}.[[start_at]] >= {{%splatfest3}}.[[start_at]] - '1 hour'::interval",
                    "{{%battle3}}.[[start_at]] <= {{%splatfest3}}.[[end_at]] + '1 hour'::interval",
                ]),
            )
            ->andWhere([
                '{{%battle3}}.[[has_disconnect]]' => false,
                '{{%battle3}}.[[is_automated]]' => true,
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                '{{%battle3}}.[[rule_id]]' => $rule->id,
                '{{%battle3}}.[[use_for_entire]]' => true,
                '{{%splatfest3}}.[[id]]' => $targetFest->id,
            ])
            ->andWhere('{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]')
            ->groupBy(['{{%splatfest3}}.[[id]]']);

        if (!$isGlobalFest) {
            $teamNames = $this->getTeamNames($db, $targetFest);

            $query
                ->innerJoin(
                    ['theme_a' => '{{%splatfest3_theme}}'],
                    '{{%battle3}}.[[our_team_theme_id]] = {{theme_a}}.[[id]]',
                )
                ->innerJoin(
                    ['theme_b' => '{{%splatfest3_theme}}'],
                    '{{%battle3}}.[[their_team_theme_id]] = {{theme_b}}.[[id]]',
                )
                ->andWhere([
                    '{{theme_a}}.[[name]]' => $teamNames,
                    '{{theme_b}}.[[name]]' => $teamNames,
                ]);
        }

        Splatfest3StatsPower::deleteAll(['splatfest_id' => $targetFest->id]);
        $db
            ->createCommand(
                vsprintf('INSERT INTO %s ( %s ) %s', [
                    $db->quoteTableName(Splatfest3StatsPower::tableName()),
                    implode(
                        ', ',
                        array_map(
                            fn (string $name): string => $db->quoteColumnName($name),
                            array_keys($query->select),
                        ),
                    ),
                    $query->createCommand($db)->rawSql,
                ]),
            )
            ->execute();

        return true;
    }

    private function updatePowerStatsHistogram(
        Connection $db,
        Splatfest3 $targetFest,
        bool $isGlobalFest,
    ): bool {
        $this->stderr("Updating splatfest3_stats_power_histogram...\n");

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s.%3$s / %2$s.%4$s) + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%battle3}}'),
            $db->quoteTableName('{{%splatfest3_stats_power}}'),
            $db->quoteColumnName('fest_power'),
            $db->quoteColumnName('histogram_width'),
        );

        $lobby = Lobby3::find()
            ->andWhere(['key' => 'splatfest_challenge'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        $rule = Rule3::find()
            ->andWhere(['key' => 'nawabari'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        if (!$lobby || !$rule) {
            throw new LogicException('Lobby or Rule not found');
        }

        $query = (new Query())
            ->select([
                'splatfest_id' => '{{%splatfest3}}.[[id]]',
                'class_value' => $classValue,
                'battles' => 'COUNT(*)',
            ])
            ->from('{{%battle3}}')
            ->innerJoin(
                '{{%splatfest3}}',
                implode(' AND ', [
                    "{{%battle3}}.[[start_at]] >= {{%splatfest3}}.[[start_at]] - '1 hour'::interval",
                    "{{%battle3}}.[[start_at]] <= {{%splatfest3}}.[[end_at]] + '1 hour'::interval",
                ]),
            )
            ->innerJoin(
                '{{%splatfest3_stats_power}}',
                '{{%splatfest3}}.[[id]] = {{%splatfest3_stats_power}}.[[splatfest_id]]',
            )
            ->andWhere([
                '{{%battle3}}.[[has_disconnect]]' => false,
                '{{%battle3}}.[[is_automated]]' => true,
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                '{{%battle3}}.[[rule_id]]' => $rule->id,
                '{{%battle3}}.[[use_for_entire]]' => true,
                '{{%splatfest3}}.[[id]]' => $targetFest->id,
            ])
            ->andWhere('{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]')
            ->andWhere(['not', ['{{%battle3}}.[[fest_power]]' => null]])
            ->groupBy([
                '{{%splatfest3}}.[[id]]',
                $classValue,
            ]);

        if (!$isGlobalFest) {
            $teamNames = $this->getTeamNames($db, $targetFest);

            $query
                ->innerJoin(
                    ['theme_a' => '{{%splatfest3_theme}}'],
                    '{{%battle3}}.[[our_team_theme_id]] = {{theme_a}}.[[id]]',
                )
                ->innerJoin(
                    ['theme_b' => '{{%splatfest3_theme}}'],
                    '{{%battle3}}.[[their_team_theme_id]] = {{theme_b}}.[[id]]',
                )
                ->andWhere([
                    '{{theme_a}}.[[name]]' => $teamNames,
                    '{{theme_b}}.[[name]]' => $teamNames,
                ]);
        }

        Splatfest3StatsPowerHistogram::deleteAll(['splatfest_id' => $targetFest->id]);
        $db
            ->createCommand(
                vsprintf('INSERT INTO %s ( %s ) %s', [
                    $db->quoteTableName(Splatfest3StatsPowerHistogram::tableName()),
                    implode(
                        ', ',
                        array_map(
                            fn (string $name): string => $db->quoteColumnName($name),
                            array_keys($query->select),
                        ),
                    ),
                    $query->createCommand($db)->rawSql,
                ]),
            )
            ->execute();

        return true;
    }

    /**
     * @return string[]
     */
    private function getTeamNames(Connection $db, Splatfest3 $fest): array
    {
        $langs = ArrayHelper::getColumn(
            Language::find()->standard()->cache(86400)->all(),
            'lang',
        );

        $results = [];
        foreach ($fest->splatfestTeam3s as $team) {
            $results = array_merge($results, array_map(
                fn (string $lang): string => Yii::t('db/splatfest3/team', $team->name, [], $lang),
                $langs,
            ));
        }

        return array_values(array_unique($results));
    }

    /**
     * @param Splatfest3[] $targetFests
     */
    private function updateWeaponStats(Connection $db, array $targetFests): bool
    {
        $this->stderr("Updating splatfest3_stats_weapon...\n");

        Splatfest3StatsWeapon::deleteAll([
            'fest_id' => ArrayHelper::getColumn($targetFests, 'id'),
        ]);

        $stats = fn (string $key): array => [
            "avg_{$key}" => "AVG({{%battle_player3}}.[[{$key}]])",
            "sd_{$key}" => "STDDEV_SAMP({{%battle_player3}}.[[{$key}]])",
            "min_{$key}" => "MIN({{%battle_player3}}.[[{$key}]])",
            "p05_{$key}" => "PERCENTILE_CONT(0.05) WITHIN GROUP (ORDER BY {{%battle_player3}}.[[{$key}]])",
            "p25_{$key}" => "PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY {{%battle_player3}}.[[{$key}]])",
            "p50_{$key}" => "PERCENTILE_CONT(0.50) WITHIN GROUP (ORDER BY {{%battle_player3}}.[[{$key}]])",
            "p75_{$key}" => "PERCENTILE_CONT(0.75) WITHIN GROUP (ORDER BY {{%battle_player3}}.[[{$key}]])",
            "p95_{$key}" => "PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY {{%battle_player3}}.[[{$key}]])",
            "max_{$key}" => "MAX({{%battle_player3}}.[[{$key}]])",
            "mode_{$key}" => "MODE() WITHIN GROUP (ORDER BY {{%battle_player3}}.[[{$key}]])",
        ];

        $query = (new Query())
            ->select(
                array_merge(
                    [
                        'fest_id' => '{{%splatfest3}}.[[id]]',
                        'lobby_id' => '{{%battle3}}.[[lobby_id]]',
                        'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
                        'battles' => 'COUNT(*)',
                        'wins' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ],
                    $stats('kill'),
                    $stats('assist'),
                    $stats('death'),
                    $stats('special'),
                    $stats('inked'),
                ),
            )
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin(
                '{{%splatfest3}}',
                implode(' AND ', [
                    '{{%battle3}}.[[start_at]] >= {{%splatfest3}}.[[start_at]]',
                    '{{%battle3}}.[[start_at]] < {{%splatfest3}}.[[end_at]]',
                ]),
            )
            ->innerJoin(
                '{{%battle_player3}}',
                '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]',
            )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => ArrayHelper::getColumn(
                        Lobby3::find()
                            ->andWhere(['key' => ['splatfest_challenge', 'splatfest_open']])
                            ->all(),
                        'id',
                    ),
                    '{{%battle3}}.[[rule_id]]' => ArrayHelper::getColumn(
                        Rule3::find()
                            ->andWhere(['key' => 'nawabari'])
                            ->all(),
                        'id',
                    ),
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                    '{{%splatfest3}}.[[id]]' => ArrayHelper::getColumn($targetFests, 'id'),
                ],
                ['not', ['{{%battle_player3}}.[[assist]]' => null]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[inked]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
            ])
            ->groupBy([
                '{{%splatfest3}}.[[id]]',
                '{{%battle3}}.[[lobby_id]]',
                '{{%battle_player3}}.[[weapon_id]]',
            ]);

        $db
            ->createCommand(
                vsprintf('INSERT INTO %s ( %s ) %s', [
                    $db->quoteTableName('{{%splatfest3_stats_weapon}}'),
                    implode(
                        ', ',
                        array_map(
                            $db->quoteColumnName(...),
                            array_keys($query->select),
                        ),
                    ),
                    $query->createCommand($db)->rawSql,
                ]),
            )
            ->execute();

        return true;
    }

    private function vacuumStatsTables(Connection $db): bool
    {
        $tables = [
            Splatfest3StatsPower::tableName(),
            Splatfest3StatsPowerHistogram::tableName(),
            Splatfest3StatsWeapon::tableName(),
        ];

        foreach ($tables as $table) {
            $this->vacuumTable($db, $table);
        }

        return true;
    }

    private function vacuumTable(Connection $db, string $table): bool
    {
        $this->stderr("Vacuuming {$table}...\n");
        $db->createCommand('VACUUM ( ANALYZE ) ' . $db->quoteTableName($table))->execute();
        return true;
    }
}
