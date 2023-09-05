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

final class m230905_113827_stat_bigrun_job_distrib extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s ( %s ) WHERE ((%s))', [
                $db->quoteColumnName('ix_salmon3_bigrun_stats'),
                $db->quoteTableName('{{%salmon3}}'),
                implode(', ', [
                    $db->quoteColumnName('schedule_id'),
                ]),
                implode(') AND (', [
                    '{{%salmon3}}.[[clear_waves]] BETWEEN 0 AND 3',
                    '{{%salmon3}}.[[golden_eggs]] IS NOT NULL',
                    '{{%salmon3}}.[[has_broken_data]] = FALSE',
                    '{{%salmon3}}.[[has_disconnect]] = FALSE',
                    '{{%salmon3}}.[[is_automated]] = TRUE',
                    '{{%salmon3}}.[[is_big_run]] = TRUE',
                    '{{%salmon3}}.[[is_deleted]] = FALSE',
                    '{{%salmon3}}.[[is_eggstra_work]] = FALSE',
                    '{{%salmon3}}.[[is_private]] = FALSE',
                ]),
            ]),
        );

        $this->createTable('{{%stat_bigrun_distrib_job_abstract3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'jobs' => $this->bigInteger()->notNull(),
            'average' => $this->double()->notNull(),
            'stddev' => $this->double()->null(),
            'clear_jobs' => $this->bigInteger()->notNull(),
            'clear_average' => $this->double()->null(),
            'clear_stddev' => $this->double()->null(),
            'min' => $this->integer()->null(),
            'p05' => $this->integer()->null(),
            'p25' => $this->integer()->null(),
            'p50' => $this->integer()->null(),
            'p75' => $this->integer()->null(),
            'p80' => $this->integer()->null(),
            'p95' => $this->integer()->null(),
            'max' => $this->integer()->null(),
            'histogram_width' => $this->integer()->null(),

            'PRIMARY KEY ([[schedule_id]])',
        ]);

        $this->createTable('{{%stat_bigrun_distrib_job_histogram3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'count' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]], [[class_value]])',
        ]);

        $p = fn (float $pct): string => sprintf(
            'PERCENTILE_DISC(%.02f) WITHIN GROUP (ORDER BY %s)',
            $pct,
            '{{%salmon3}}.[[golden_eggs]]',
        );

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                '{{%stat_bigrun_distrib_job_abstract3}}',
                (new Query())
                    ->select([
                        'schedule_id' => '{{%salmon3}}.[[schedule_id]]',
                        'users' => 'COUNT(DISTINCT {{%salmon3}}.[[user_id]])',
                        'jobs' => 'COUNT(*)',
                        'average' => 'AVG({{%salmon3}}.[[golden_eggs]])',
                        'stddev' => 'STDDEV_SAMP({{%salmon3}}.[[golden_eggs]])',
                        'clear_jobs' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] = 3 THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                        'clear_average' => vsprintf('AVG(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] = 3 THEN {{%salmon3}}.[[golden_eggs]]',
                                'ELSE NULL',
                            ]),
                        ]),
                        'clear_stddev' => vsprintf('STDDEV_SAMP(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%salmon3}}.[[clear_waves]] = 3 THEN {{%salmon3}}.[[golden_eggs]]',
                                'ELSE NULL',
                            ]),
                        ]),
                        'min' => 'MIN({{%salmon3}}.[[golden_eggs]])',
                        'p05' => $p(0.05),
                        'p25' => $p(0.25),
                        'p50' => $p(0.50),
                        'p75' => $p(0.75),
                        'p80' => $p(0.80),
                        'p95' => $p(0.95),
                        'max' => 'MAX({{%salmon3}}.[[golden_eggs]])',
                        'histogram_width' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                            'COUNT(*)',
                            'STDDEV_SAMP({{%salmon3}}.[[golden_eggs]])',
                        ]),
                    ])
                    ->from('{{%salmon3}}')
                    ->innerJoin(
                        '{{%salmon_schedule3}}',
                        vsprintf('((%s))', [
                            implode(') AND (', [
                                '{{%salmon3}}.[[schedule_id]] = {{%salmon_schedule3}}.[[id]]',
                                '{{%salmon_schedule3}}.[[big_map_id]] IS NOT NULL',
                                '{{%salmon_schedule3}}.[[is_eggstra_work]] = FALSE',
                                '{{%salmon_schedule3}}.[[map_id]] IS NULL',
                            ]),
                        ]),
                    )
                    ->andWhere(['and',
                        [
                            '{{%salmon3}}.[[has_broken_data]]' => false,
                            '{{%salmon3}}.[[has_disconnect]]' => false,
                            '{{%salmon3}}.[[is_automated]]' => true,
                            '{{%salmon3}}.[[is_big_run]]' => true,
                            '{{%salmon3}}.[[is_deleted]]' => false,
                            '{{%salmon3}}.[[is_eggstra_work]]' => false,
                            '{{%salmon3}}.[[is_private]]' => false,
                        ],
                        ['not', ['{{%salmon3}}.[[golden_eggs]]' => null]],
                        ['between', '{{%salmon3}}.[[clear_waves]]', 0, 3],
                    ])
                    ->groupBy([
                        '{{%salmon3}}.[[schedule_id]]',
                    ])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s.%3$s / %2$s.%4$s) + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%salmon3}}'),
            $db->quoteTableName('{{%stat_bigrun_distrib_job_abstract3}}'),
            $db->quoteColumnName('golden_eggs'),
            $db->quoteColumnName('histogram_width'),
        );

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                '{{%stat_bigrun_distrib_job_histogram3}}',
                (new Query())
                    ->select([
                        'schedule_id' => '{{%salmon3}}.[[schedule_id]]',
                        'class_value' => $classValue,
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%salmon3}}')
                    ->innerJoin(
                        '{{%salmon_schedule3}}',
                        vsprintf('((%s))', [
                            implode(') AND (', [
                                '{{%salmon3}}.[[schedule_id]] = {{%salmon_schedule3}}.[[id]]',
                                '{{%salmon_schedule3}}.[[big_map_id]] IS NOT NULL',
                                '{{%salmon_schedule3}}.[[is_eggstra_work]] = FALSE',
                                '{{%salmon_schedule3}}.[[map_id]] IS NULL',
                            ]),
                        ]),
                    )
                    ->innerJoin(
                        '{{%stat_bigrun_distrib_job_abstract3}}',
                        '{{%salmon3}}.[[schedule_id]] = {{%stat_bigrun_distrib_job_abstract3}}.[[schedule_id]]',
                    )
                    ->andWhere(['and',
                        [
                            '{{%salmon3}}.[[has_broken_data]]' => false,
                            '{{%salmon3}}.[[has_disconnect]]' => false,
                            '{{%salmon3}}.[[is_automated]]' => true,
                            '{{%salmon3}}.[[is_big_run]]' => true,
                            '{{%salmon3}}.[[is_deleted]]' => false,
                            '{{%salmon3}}.[[is_eggstra_work]]' => false,
                            '{{%salmon3}}.[[is_private]]' => false,
                        ],
                        ['not', ['{{%salmon3}}.[[golden_eggs]]' => null]],
                        ['between', '{{%salmon3}}.[[clear_waves]]', 0, 3],
                    ])
                    ->groupBy([
                        '{{%salmon3}}.[[schedule_id]]',
                        $classValue,
                    ])
                    ->createCommand($db)
                    ->rawSql,
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
            '{{%stat_bigrun_distrib_job_histogram3}}',
            '{{%stat_bigrun_distrib_job_abstract3}}',
        ]);

        $db->dropIndex('ix_salmon3_bigrun_stats', '{{%salmon3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_bigrun_distrib_job_abstract3}}',
            '{{%stat_bigrun_distrib_job_histogram3}}',
        ];
    }
}
