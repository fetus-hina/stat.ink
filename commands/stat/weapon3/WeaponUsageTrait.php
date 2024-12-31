<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\weapon3;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\SplatoonVersion3;
use app\models\SplatoonVersionGroup3;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use app\models\StatWeapon3XUsagePerVersion;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function fwrite;
use function implode;
use function sprintf;
use function vsprintf;

use const SORT_ASC;
use const STDERR;

trait WeaponUsageTrait
{
    protected function makeStatWeapon3Usage(): void
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            return;
        }

        $db->transaction(
            fn (Connection $db) => $this->makeStatWeapon3UsageImpl($db),
            Transaction::REPEATABLE_READ,
        );

        $tables = [
            StatWeapon3Usage::tableName(),
            StatWeapon3UsagePerVersion::tableName(),
            StatWeapon3XUsage::tableName(),
            StatWeapon3XUsagePerVersion::tableName(),
        ];
        foreach ($tables as $table) {
            fwrite(STDERR, "Vacuuming {$table} ...\n");
            $sql = vsprintf('VACUUM ( ANALYZE ) %s', [
                $db->quoteTableName($table),
            ]);
            $db->createCommand($sql)->execute();
        }
        fwrite(STDERR, "Update done\n");
    }

    private function makeStatWeapon3UsageImpl(Connection $db): void
    {
        $this->makeStatWeapon3UsagePerSeason($db);
        $this->makeStatWeapon3UsagePerVersion($db);
        $this->makeStatWeapon3XUsagePerSeason($db);
        $this->makeStatWeapon3XUsagePerVersion($db);
    }

    private function makeStatWeapon3UsagePerSeason(Connection $db): void
    {
        fwrite(STDERR, "Updating stat_weapon3_usage...\n");

        $targetSeasons = $this->getStatWeapon3UsageTargetSeasons($db);
        StatWeapon3Usage::deleteAll([
            'season_id' => ArrayHelper::getColumn($targetSeasons, 'id'),
        ]);

        $select = $this->buildSelectForWeapon3UsagePerSeason($db, $targetSeasons);
        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName(StatWeapon3Usage::tableName()),
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute($db);
    }

    private function makeStatWeapon3UsagePerVersion(Connection $db): void
    {
        fwrite(STDERR, "Updating stat_weapon3_usage_per_version...\n");

        $targetVersions = $this->getStatWeapon3UsageTargetVersions($db);
        StatWeapon3UsagePerVersion::deleteAll([
            'version_id' => ArrayHelper::getColumn($targetVersions, 'id'),
        ]);

        $select = $this->buildSelectForWeapon3UsagePerVersion($db, $targetVersions);
        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName(StatWeapon3UsagePerVersion::tableName()),
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute($db);
    }

    private function makeStatWeapon3XUsagePerSeason(Connection $db): void
    {
        fwrite(STDERR, "Updating stat_weapon3_x_usage...\n");

        $targetSeasons = $this->getStatWeapon3UsageTargetSeasons($db);
        StatWeapon3XUsage::deleteAll([
            'season_id' => ArrayHelper::getColumn($targetSeasons, 'id'),
        ]);

        $select = $this->buildSelectForWeapon3XUsagePerSeason($db, $targetSeasons);
        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName(StatWeapon3XUsage::tableName()),
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute($db);
    }

    private function makeStatWeapon3XUsagePerVersion(Connection $db): void
    {
        fwrite(STDERR, "Updating stat_weapon3_x_usage_per_version...\n");

        $targetVersions = $this->getStatWeapon3UsageTargetVersionGroups($db);
        StatWeapon3XUsagePerVersion::deleteAll([
            'version_group_id' => ArrayHelper::getColumn($targetVersions, 'id'),
        ]);

        $select = $this->buildSelectForWeapon3XUsagePerVersion($db, $targetVersions);
        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName(StatWeapon3XUsagePerVersion::tableName()),
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute($db);
    }

    /**
     * @return Season3[]
     */
    private function getStatWeapon3UsageTargetSeasons(Connection $db): array
    {
        $date = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->sub(new DateInterval('P30D'));

        $minModel = Season3::find()
            ->andWhere(
                vsprintf('[[term]] @> %s::TIMESTAMPTZ', [
                    $db->quoteValue($date->format(DateTimeInterface::ATOM)),
                ]),
            )
            ->limit(1)
            ->one($db);
        if (!$minModel) {
            throw new Exception();
        }

        return Season3::find()
            ->andWhere(['>=', 'start_at', $minModel->start_at])
            ->orderBy(['start_at' => SORT_ASC])
            ->all($db);
    }

    /**
     * @return SplatoonVersion3[]
     */
    private function getStatWeapon3UsageTargetVersions(Connection $db): array
    {
        $date = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->sub(new DateInterval('P30D'));

        return SplatoonVersion3::find()
            ->andWhere(['>=', 'release_at', $date->format(DateTimeInterface::ATOM)])
            ->orderBy(['release_at' => SORT_ASC])
            ->all($db);
    }

    /**
     * @return SplatoonVersionGroup3[]
     */
    private function getStatWeapon3UsageTargetVersionGroups(Connection $db): array
    {
        $versions = $this->getStatWeapon3UsageTargetVersions($db);

        return SplatoonVersionGroup3::find()
            ->andWhere(['id' => ArrayHelper::getColumn($versions, 'group_id')])
            ->all($db);
    }

    /**
     * @param Season3[] $seasons
     */
    private function buildSelectForWeapon3UsagePerSeason(Connection $db, array $seasons): Query
    {
        return $this->buildSelectForWeapon3UsageImpl($db, seasons: $seasons);
    }

    /**
     * @param Season3[] $seasons
     */
    private function buildSelectForWeapon3XUsagePerSeason(Connection $db, array $seasons): Query
    {
        return $this->buildSelectForWeapon3UsageImpl($db, seasons: $seasons, xUsage: true);
    }

    /**
     * @param SplatoonVersionGroup3[] $versions
     */
    private function buildSelectForWeapon3XUsagePerVersion(Connection $db, array $versions): Query
    {
        return $this->buildSelectForWeapon3UsageImpl($db, versions: $versions, xUsage: true);
    }

    /**
     * @param SplatoonVersion3[] $versions
     */
    private function buildSelectForWeapon3UsagePerVersion(Connection $db, array $versions): Query
    {
        return $this->buildSelectForWeapon3UsageImpl($db, versions: $versions);
    }

    /**
     * @param Season3[]|null $seasons
     * @param SplatoonVersion3[]|SplatoonVersionGroup3[]|null $versions
     */
    private function buildSelectForWeapon3UsageImpl(
        Connection $db,
        ?array $seasons = null,
        ?array $versions = null,
        bool $xUsage = false,
    ): Query {
        $lobbies = ArrayHelper::map(
            Lobby3::find()->all($db),
            'key',
            'id',
        );

        $tricolor = Rule3::find()
            ->andWhere(['key' => 'tricolor'])
            ->limit(1)
            ->one();
        if (!$tricolor) {
            throw new Exception();
        }

        $selectPkey = array_filter(
            [
                'season_id' => $seasons !== null ? '{{%season3}}.[[id]]' : null,
                'version_id' => $versions && $versions[0] instanceof SplatoonVersion3
                    ? '{{%battle3}}.[[version_id]]'
                    : null,
                'version_group_id' => $versions && $versions[0] instanceof SplatoonVersionGroup3
                    ? '{{%splatoon_version3}}.[[group_id]]'
                    : null,
                'lobby_id' => $xUsage
                    ? null
                    : vsprintf('(CASE %s END)', [
                        implode(' ', [
                            vsprintf('WHEN {{%%battle3}}.[[lobby_id]] = %d THEN %d', [
                                $lobbies['splatfest_challenge'],
                                $lobbies['regular'],
                            ]),
                            'ELSE {{%battle3}}.[[lobby_id]]',
                        ]),
                    ]),
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'range_id' => $xUsage
                    ? '{{%stat_weapon3_x_usage_range}}.[[id]]'
                    : null,
                'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
            ],
            fn (mixed $v): bool => $v !== null,
        );

        $select = (new Query())
            ->select(array_merge(
                $selectPkey,
                [
                    'battles' => 'COUNT(*)',
                    'wins' => vsprintf('SUM(%s)', [
                        vsprintf('CASE %s END', [
                            implode(' ', [
                                'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ]),
                    'seconds' => vsprintf('SUM(%s)', [
                        vsprintf('%s - %s', [
                            'EXTRACT(EPOCH FROM {{%battle3}}.[[end_at]])',
                            'EXTRACT(EPOCH FROM {{%battle3}}.[[start_at]])',
                        ]),
                    ]),
                ],
                $this->buildSelectMetricsForWeapon3Usage('kill', '{{%battle_player3}}.[[kill]]'),
                $this->buildSelectMetricsForWeapon3Usage('assist', '{{%battle_player3}}.[[assist]]'),
                $this->buildSelectMetricsForWeapon3Usage('death', '{{%battle_player3}}.[[death]]'),
                $this->buildSelectMetricsForWeapon3Usage('special', '{{%battle_player3}}.[[special]]'),
                $this->buildSelectMetricsForWeapon3Usage('inked', '{{%battle_player3}}.[[inked]]', false),
            ))
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $xUsage
                        ? $lobbies['xmatch']
                        : [
                            $lobbies['bankara_challenge'],
                            $lobbies['regular'],
                            $lobbies['splatfest_challenge'],
                            $lobbies['xmatch'],
                        ],
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => $tricolor->id]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                ['not', ['{{%battle_player3}}.[[assist]]' => null]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[inked]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->groupBy(array_values($selectPkey));

        if ($seasons !== null) {
            $select->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]');
            $select->andWhere(['{{%season3}}.[[id]]' => ArrayHelper::getColumn($seasons, 'id')]);
        }

        if ($versions !== null) {
            if (!$versions) {
                $select->andWhere('1 = 0');
            } elseif ($versions[0] instanceof SplatoonVersionGroup3) {
                $select->innerJoin(
                    '{{%splatoon_version3}}',
                    '{{%battle3}}.[[version_id]] = {{%splatoon_version3}}.[[id]]',
                );
                $select->andWhere([
                    '{{%splatoon_version3}}.[[group_id]]' => ArrayHelper::getColumn($versions, 'id'),
                ]);
            } else {
                $select->andWhere([
                    '{{%battle3}}.[[version_id]]' => ArrayHelper::getColumn($versions, 'id'),
                ]);
            }
        }

        if ($xUsage) {
            $select
                ->innerJoin(
                    '{{%stat_weapon3_x_usage_term}}',
                    '{{%battle3}}.[[start_at]] <@ {{%stat_weapon3_x_usage_term}}.[[term]]',
                )
                ->innerJoin(
                    '{{%stat_weapon3_x_usage_range}}',
                    implode(' AND ', [
                        '{{%stat_weapon3_x_usage_term}}.[[id]] = {{%stat_weapon3_x_usage_range}}.[[term_id]]',
                        '{{%battle3}}.[[x_power_before]] <@ {{%stat_weapon3_x_usage_range}}.[[x_power_range]]',
                    ]),
                )
                ->andWhere(['not', ['{{%battle3}}.[[x_power_before]]' => null]]);
        }

        return $select;
    }

    private function buildSelectMetricsForWeapon3Usage(string $baseName, string $column, bool $mode = true): array
    {
        $percentile = fn (string $column, int $percentile): string => sprintf(
            'PERCENTILE_CONT(%.2f) WITHIN GROUP (ORDER BY %s)',
            $percentile / 100.0,
            $column,
        );

        $columns = [
            "avg_{$baseName}" => "AVG({$column})",
            "sd_{$baseName}" => "STDDEV_SAMP({$column})",
            "min_{$baseName}" => "MIN({$column})",
            "p05_{$baseName}" => $percentile($column, 5),
            "p25_{$baseName}" => $percentile($column, 25),
            "p50_{$baseName}" => $percentile($column, 50),
            "p75_{$baseName}" => $percentile($column, 75),
            "p95_{$baseName}" => $percentile($column, 95),
            "max_{$baseName}" => "MAX({$column})",
        ];

        return array_merge(
            $columns,
            $mode ? ["mode_{$baseName}" => "MODE() WITHIN GROUP (ORDER BY {$column})"] : [],
        );
    }
}
