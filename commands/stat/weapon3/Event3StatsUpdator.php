<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\weapon3;

use Yii;
use app\components\helpers\TypeHelper;
use app\models\Event3StatsPower;
use app\models\Event3StatsPowerHistogram;
use app\models\Event3StatsPowerPeriod;
use app\models\Event3StatsPowerPeriodHistogram;
use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;
use app\models\Lobby3;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function fwrite;
use function implode;
use function sprintf;
use function vsprintf;

use const STDERR;

final class Event3StatsUpdator
{
    public static function update(): void
    {
        $db = TypeHelper::instanceOf(Yii::$app->db, Connection::class);
        $db->transaction(
            fn (Connection $db) => self::doUpdate($db),
            Transaction::REPEATABLE_READ,
        );
        self::vacuumTables($db);

        fwrite(STDERR, "Done.\n");
    }

    private static function doUpdate(Connection $db): void
    {
        self::updateStats(
            $db,
            Event3StatsWeapon::tableName(),
            'weapon_id',
            '{{%weapon3}}.[[canonical_id]]',
        );

        self::updateStats(
            $db,
            Event3StatsSpecial::tableName(),
            'special_id',
            '{{%weapon3}}.[[special_id]]',
        );

        self::updatePowerAbstract($db);
        self::updatePowerHistogram($db);
        self::updatePowerPeriodAbstract($db);
        self::updatePowerPeriodHistogram($db);
    }

    private static function updateStats(
        Connection $db,
        string $dstTableName,
        string $dstColumnName,
        string $groupColumnName,
    ): void {
        fwrite(STDERR, "Updating $dstTableName...\n");

        $lobby = TypeHelper::instanceOf(Lobby3::findOne(['key' => 'event']), Lobby3::class);

        $select = (new Query())
            ->select(
                array_merge(
                    [
                        'schedule_id' => '{{%event_schedule3}}.[[id]]',
                        $dstColumnName => $groupColumnName,
                        'battles' => 'COUNT(*)',
                        'wins' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%battle_player3}}.[[is_our_team]] = {{%result3}}.[[is_win]] THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ],
                    self::statsColumns('kill', '{{%battle_player3}}.[[kill]]'),
                    self::statsColumns('assist', '{{%battle_player3}}.[[assist]]'),
                    self::statsColumns('death', '{{%battle_player3}}.[[death]]'),
                    self::statsColumns('special', '{{%battle_player3}}.[[special]]'),
                    self::statsColumns('inked', '{{%battle_player3}}.[[inked]]', false),
                ),
            )
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->innerJoin('{{%weapon3}}', '{{%battle_player3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
            ->innerJoin('{{%event_schedule3}}', implode(' AND ', [
                '{{%battle3}}.[[event_id]] = {{%event_schedule3}}.[[event_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_schedule3}}.[[start_at]]',
                    "{{%event_schedule3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%battle_player3}}.[[assist]]' => null]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
            ])
            ->groupBy([
                '{{%event_schedule3}}.[[id]]',
                $groupColumnName,
            ]);

        fwrite(STDERR, "(1/2) delete...\n");
        // echo $db->createCommand()->delete($dstTableName)->rawSql . "\n";
        $db->createCommand()->delete($dstTableName)->execute();

        fwrite(STDERR, "(2/2) insert...\n");
        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName($dstTableName),
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand()->rawSql,
        ]);
        // echo $sql . "\n";
        $db->createCommand($sql)->execute();
    }

    /**
     * @return array<string, string>
     */
    private static function statsColumns(string $name, string $srcColumn, bool $mode = true): array
    {
        return array_filter(
            [
                "avg_{$name}" => "AVG($srcColumn)",
                "sd_{$name}" => "STDDEV_SAMP($srcColumn)",
                "min_{$name}" => "MIN($srcColumn)",
                "p05_{$name}" => "PERCENTILE_DISC(0.05) WITHIN GROUP (ORDER BY $srcColumn)",
                "p25_{$name}" => "PERCENTILE_DISC(0.25) WITHIN GROUP (ORDER BY $srcColumn)",
                "p50_{$name}" => "PERCENTILE_DISC(0.50) WITHIN GROUP (ORDER BY $srcColumn)",
                "p75_{$name}" => "PERCENTILE_DISC(0.75) WITHIN GROUP (ORDER BY $srcColumn)",
                "p95_{$name}" => "PERCENTILE_DISC(0.95) WITHIN GROUP (ORDER BY $srcColumn)",
                "max_{$name}" => "MAX($srcColumn)",
                "mode_{$name}" => $mode ? "MODE() WITHIN GROUP (ORDER BY $srcColumn)" : null,
            ],
            fn (?string $v): bool => $v !== null,
        );
    }

    private static function updatePowerAbstract(Connection $db): void
    {
        fwrite(STDERR, "Updating event3_stats_power...\n");

        $lobby = TypeHelper::instanceOf(Lobby3::findOne(['key' => 'event']), Lobby3::class);

        $p = fn (float $p): string => vsprintf(
            'PERCENTILE_CONT(%.2f) WITHIN GROUP (ORDER BY %s.%s)',
            [
                $p,
                $db->quoteTableName('{{%battle3}}'),
                $db->quoteColumnName('event_power'),
            ],
        );

        $select = (new Query())
            ->select([
                'schedule_id' => '{{%event_schedule3}}.[[id]]',
                'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
                'battles' => 'COUNT(*)',
                'agg_battles' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%battle3}}.[[event_power]] IS NOT NULL THEN 1',
                        'ELSE 0',
                    ]),
                ]),
                'average' => 'AVG({{%battle3}}.[[event_power]])',
                'stddev' => 'STDDEV_SAMP({{%battle3}}.[[event_power]])',
                'minimum' => 'MIN({{%battle3}}.[[event_power]])',
                'p05' => $p(0.05),
                'p25' => $p(0.25),
                'p50' => $p(0.50),
                'p75' => $p(0.75),
                'p80' => $p(0.80),
                'p95' => $p(0.95),
                'maximum' => 'MAX({{%battle3}}.[[event_power]])',
                'histogram_width' => vsprintf('ROUND(%s / 2) * 2', [
                    vsprintf('((3.5 * STDDEV_SAMP(%s)) / POWER(%s, 1.0 / 3.0))', [
                        '{{%battle3}}.[[event_power]]',
                        vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%battle3}}.[[event_power]] IS NOT NULL THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ]),
                ]),
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%event_schedule3}}', implode(' AND ', [
                '{{%battle3}}.[[event_id]] = {{%event_schedule3}}.[[event_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_schedule3}}.[[start_at]]',
                    "{{%event_schedule3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
            ])
            ->groupBy(['{{%event_schedule3}}.[[id]]'])
            ->having(
                vsprintf('SUM(CASE %s END) > 0', [
                    implode(' ', [
                        'WHEN {{%battle3}}.[[event_power]] IS NOT NULL THEN 1',
                        'ELSE 0',
                    ]),
                ]),
            );

        Event3StatsPower::deleteAll();
        $db->createCommand(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName(Event3StatsPower::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        )->execute();
    }

    private static function updatePowerHistogram(Connection $db): void
    {
        fwrite(STDERR, "Updating event3_stats_power_histogram...\n");

        $lobby = TypeHelper::instanceOf(Lobby3::findOne(['key' => 'event']), Lobby3::class);

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '(FLOOR(%1$s.%3$s / %2$s.%4$s + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%battle3}}'),
            $db->quoteTableName('{{%event3_stats_power}}'),
            $db->quoteColumnName('event_power'),
            $db->quoteColumnName('histogram_width'),
        );

        $select = (new Query())
            ->select([
                'schedule_id' => '{{%event_schedule3}}.[[id]]',
                'class_value' => $classValue,
                'battles' => 'COUNT(*)',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%event_schedule3}}', implode(' AND ', [
                '{{%battle3}}.[[event_id]] = {{%event_schedule3}}.[[event_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_schedule3}}.[[start_at]]',
                    "{{%event_schedule3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->innerJoin(
                '{{%event3_stats_power}}',
                '{{%event_schedule3}}.[[id]] = {{%event3_stats_power}}.[[schedule_id]]',
            )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[event_power]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
                ['>', '{{%event3_stats_power}}.[[histogram_width]]', 0],
            ])
            ->groupBy([
                '{{%event_schedule3}}.[[id]]',
                $classValue,
            ]);

        Event3StatsPowerHistogram::deleteAll();
        $db->createCommand(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName(Event3StatsPowerHistogram::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        )->execute();
    }

    private static function updatePowerPeriodAbstract(Connection $db): void
    {
        fwrite(STDERR, "Updating event3_stats_power_period...\n");

        $lobby = TypeHelper::instanceOf(Lobby3::findOne(['key' => 'event']), Lobby3::class);

        $p = fn (float $p): string => vsprintf(
            'PERCENTILE_CONT(%.2f) WITHIN GROUP (ORDER BY %s.%s)',
            [
                $p,
                $db->quoteTableName('{{%battle3}}'),
                $db->quoteColumnName('event_power'),
            ],
        );

        $select = (new Query())
            ->select([
                'period_id' => '{{%event_period3}}.[[id]]',
                'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
                'battles' => 'COUNT(*)',
                'agg_battles' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%battle3}}.[[event_power]] IS NOT NULL THEN 1',
                        'ELSE 0',
                    ]),
                ]),
                'average' => 'AVG({{%battle3}}.[[event_power]])',
                'stddev' => 'STDDEV_SAMP({{%battle3}}.[[event_power]])',
                'minimum' => 'MIN({{%battle3}}.[[event_power]])',
                'p05' => $p(0.05),
                'p25' => $p(0.25),
                'p50' => $p(0.50),
                'p75' => $p(0.75),
                'p80' => $p(0.80),
                'p95' => $p(0.95),
                'maximum' => 'MAX({{%battle3}}.[[event_power]])',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%event_schedule3}}', implode(' AND ', [
                '{{%battle3}}.[[event_id]] = {{%event_schedule3}}.[[event_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_schedule3}}.[[start_at]]',
                    "{{%event_schedule3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->innerJoin('{{%event_period3}}', implode(' AND ', [
                '{{%event_schedule3}}.[[id]] = {{%event_period3}}.[[schedule_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_period3}}.[[start_at]]',
                    "{{%event_period3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
            ])
            ->groupBy(['{{%event_period3}}.[[id]]'])
            ->having(
                vsprintf('SUM(CASE %s END) > 0', [
                    implode(' ', [
                        'WHEN {{%battle3}}.[[event_power]] IS NOT NULL THEN 1',
                        'ELSE 0',
                    ]),
                ]),
            );

        Event3StatsPowerPeriod::deleteAll();
        $db->createCommand(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName(Event3StatsPowerPeriod::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        )->execute();
    }

    private static function updatePowerPeriodHistogram(Connection $db): void
    {
        fwrite(STDERR, "Updating event3_stats_power_period_histogram...\n");

        $lobby = TypeHelper::instanceOf(Lobby3::findOne(['key' => 'event']), Lobby3::class);

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '(FLOOR(%1$s.%3$s / %2$s.%4$s + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%battle3}}'),
            $db->quoteTableName('{{%event3_stats_power}}'),
            $db->quoteColumnName('event_power'),
            $db->quoteColumnName('histogram_width'),
        );

        $select = (new Query())
            ->select([
                'period_id' => '{{%event_period3}}.[[id]]',
                'class_value' => $classValue,
                'battles' => 'COUNT(*)',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%event_schedule3}}', implode(' AND ', [
                '{{%battle3}}.[[event_id]] = {{%event_schedule3}}.[[event_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_schedule3}}.[[start_at]]',
                    "{{%event_schedule3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->innerJoin('{{%event_period3}}', implode(' AND ', [
                '{{%event_schedule3}}.[[id]] = {{%event_period3}}.[[schedule_id]]',
                vsprintf('(%s BETWEEN %s AND %s)', [
                    '{{%battle3}}.[[start_at]]',
                    '{{%event_period3}}.[[start_at]]',
                    "{{%event_period3}}.[[end_at]] + '10 minutes'::interval",
                ]),
            ]))
            ->innerJoin(
                '{{%event3_stats_power}}',
                '{{%event_schedule3}}.[[id]] = {{%event3_stats_power}}.[[schedule_id]]',
            )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[event_power]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
                ['>', '{{%event3_stats_power}}.[[histogram_width]]', 0],
            ])
            ->groupBy([
                '{{%event_period3}}.[[id]]',
                $classValue,
            ]);

        Event3StatsPowerPeriodHistogram::deleteAll();
        $db->createCommand(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName(Event3StatsPowerPeriodHistogram::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        )->execute();
    }

    private static function vacuumTables(Connection $db): void
    {
        fwrite(STDERR, "Vacuuming tables...\n");

        $sql = vsprintf('VACUUM ( ANALYZE ) %s', [
            implode(
                ', ',
                array_map(
                    $db->quoteTableName(...),
                    [
                        Event3StatsWeapon::tableName(),
                        Event3StatsSpecial::tableName(),
                        Event3StatsPower::tableName(),
                        Event3StatsPowerHistogram::tableName(),
                        Event3StatsPowerPeriod::tableName(),
                        Event3StatsPowerPeriodHistogram::tableName(),
                    ],
                ),
            ),
        ]);

        $db->createCommand($sql)->execute();
    }
}
