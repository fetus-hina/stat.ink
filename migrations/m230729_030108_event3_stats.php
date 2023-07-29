<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;
use yii\db\Query;

final class m230729_030108_event3_stats extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%event3_stats_power}}', [
            'schedule_id' => $this->pkRef('{{%event_schedule3}}')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'agg_battles' => $this->bigInteger()->notNull(),
            'average' => $this->double()->notNull(),
            'stddev' => $this->double()->null(),
            'minimum' => $this->float()->null(),
            'p05' => $this->float()->null(),
            'p25' => $this->float()->null(),
            'p50' => $this->float()->null(),
            'p75' => $this->float()->null(),
            'p95' => $this->float()->null(),
            'maximum' => $this->float()->null(),
            'histogram_width' => $this->integer()->null(),

            'PRIMARY KEY ([[schedule_id]])',
        ]);

        $this->createTable('{{%event3_stats_power_histogram}}', [
            'schedule_id' => $this->pkRef('{{%event_schedule3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'battles' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]], [[class_value]])',
        ]);

        $db = TypeHelper::instanceOf($this->db, Connection::class);
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
                    '{{%battle3}}.[[lobby_id]]' => $this->key2id('{{%lobby3}}', 'event'),
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
            ])
            ->groupBy(['{{%event_schedule3}}.[[id]]']);

        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName('{{%event3_stats_power}}'),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        );

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
                    '{{%battle3}}.[[lobby_id]]' => $this->key2id('{{%lobby3}}', 'event'),
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[event_id]]' => null]],
                ['not', ['{{%battle3}}.[[event_power]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%event_schedule3}}.[[id]]' => null]],
            ])
            ->groupBy([
                '{{%event_schedule3}}.[[id]]',
                $classValue,
            ]);

        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName('{{%event3_stats_power_histogram}}'),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%event3_stats_power_histogram}}',
            '{{%event3_stats_power}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%event3_stats_power}}',
            '{{%event3_stats_power_histogram}}',
        ];
    }
}
