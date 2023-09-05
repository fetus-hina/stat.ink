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

final class m230905_165722_stat_bigrun_distrib_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->createTable('{{%stat_bigrun_distrib_user_abstract3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'average' => $this->double()->notNull(),
            'stddev' => $this->double()->null(),
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

        $this->createTable('{{%stat_bigrun_distrib_user_histogram3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'count' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]], [[class_value]])',
        ]);

        $p = fn (float $pct): string => sprintf(
            'PERCENTILE_DISC(%.02f) WITHIN GROUP (ORDER BY %s)',
            $pct,
            '{{%user_stat_bigrun3}}.[[golden_eggs]]',
        );

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                '{{%stat_bigrun_distrib_user_abstract3}}',
                (new Query())
                    ->select([
                        'schedule_id' => '{{%user_stat_bigrun3}}.[[schedule_id]]',
                        'users' => 'COUNT(*)',
                        'average' => 'AVG({{%user_stat_bigrun3}}.[[golden_eggs]])',
                        'stddev' => 'STDDEV_SAMP({{%user_stat_bigrun3}}.[[golden_eggs]])',
                        'min' => 'MIN({{%user_stat_bigrun3}}.[[golden_eggs]])',
                        'p05' => $p(0.05),
                        'p25' => $p(0.25),
                        'p50' => $p(0.50),
                        'p75' => $p(0.75),
                        'p80' => $p(0.80),
                        'p95' => $p(0.95),
                        'max' => 'MAX({{%user_stat_bigrun3}}.[[golden_eggs]])',
                        'histogram_width' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                            'COUNT(*)',
                            'STDDEV_SAMP({{%user_stat_bigrun3}}.[[golden_eggs]])',
                        ]),
                    ])
                    ->from('{{%user_stat_bigrun3}}')
                    ->groupBy([
                        '{{%user_stat_bigrun3}}.[[schedule_id]]',
                    ])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s.%3$s / %2$s.%4$s) + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%user_stat_bigrun3}}'),
            $db->quoteTableName('{{%stat_bigrun_distrib_user_abstract3}}'),
            $db->quoteColumnName('golden_eggs'),
            $db->quoteColumnName('histogram_width'),
        );

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                '{{%stat_bigrun_distrib_user_histogram3}}',
                (new Query())
                    ->select([
                        'schedule_id' => '{{%user_stat_bigrun3}}.[[schedule_id]]',
                        'class_value' => $classValue,
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%user_stat_bigrun3}}')
                    ->innerJoin(
                        '{{%stat_bigrun_distrib_user_abstract3}}',
                        '{{%user_stat_bigrun3}}.[[schedule_id]] = {{%stat_bigrun_distrib_user_abstract3}}.[[schedule_id]]',
                    )
                    ->andWhere(['>', '{{%stat_bigrun_distrib_user_abstract3}}.[[histogram_width]]', 0])
                    ->groupBy([
                        '{{%user_stat_bigrun3}}.[[schedule_id]]',
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
            '{{%stat_bigrun_distrib_user_histogram3}}',
            '{{%stat_bigrun_distrib_user_abstract3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_bigrun_distrib_user_abstract3}}',
            '{{%stat_bigrun_distrib_user_histogram3}}',
        ];
    }
}
