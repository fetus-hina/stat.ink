<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

final class m230305_093448_bigrun_histogram extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%stat_bigrun_distrib3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'golden_egg' => $this->integer()->notNull(),
            'users' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]], [[golden_egg]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_bigrun_distrib3}}'),
                implode(', ', [
                    $db->quoteColumnName('schedule_id'),
                    $db->quoteColumnName('golden_egg'),
                    $db->quoteColumnName('users'),
                ]),
                (new Query())
                    ->select([
                        'schedule_id',
                        'golden_egg' => 'TRUNC([[golden_eggs]] / 5) * 5',
                        'users' => 'COUNT(*)',
                    ])
                    ->from('{{%user_stat_bigrun3}}')
                    ->groupBy([
                        'schedule_id',
                        'TRUNC([[golden_eggs]] / 5) * 5',
                    ])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        $this->createTable('{{%stat_bigrun_distrib_abstract3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'average' => $this->float()->notNull(),
            'stddev' => $this->float()->null(),
            'min' => $this->integer()->null(),
            'q1' => $this->integer()->null(),
            'median' => $this->integer()->null(),
            'q3' => $this->integer()->null(),
            'max' => $this->integer()->null(),
            'top_5_pct' => $this->integer()->null(),
            'top_20_pct' => $this->integer()->null(),

            'PRIMARY KEY ([[schedule_id]])',
        ]);

        $percentile = fn (float $pos): string => sprintf(
            'PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY [[golden_eggs]] DESC)',
            $pos,
        );

        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_bigrun_distrib_abstract3}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $columnName): string => $db->quoteColumnName($columnName),
                        [
                            'schedule_id',
                            'users',
                            'average',
                            'stddev',
                            'min',
                            'q1',
                            'median',
                            'q3',
                            'max',
                            'top_5_pct',
                            'top_20_pct',
                        ],
                    ),
                ),
                (new Query())
                    ->select([
                        'schedule_id',
                        'users' => 'COUNT(*)',
                        'average' => 'AVG([[golden_eggs]])',
                        'stddev' => 'STDDEV_SAMP([[golden_eggs]])',
                        'min' => 'MIN([[golden_eggs]])',
                        'q1' => $percentile(1 - 0.25),
                        'median' => $percentile(1 - 0.5),
                        'q3' => $percentile(1 - 0.75),
                        'max' => 'MAX([[golden_eggs]])',
                        'top_5_pct' => $percentile(0.05),
                        'top_20_pct' => $percentile(0.20),
                    ])
                    ->from('{{%user_stat_bigrun3}}')
                    ->groupBy('schedule_id')
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
            '{{%stat_bigrun_distrib3}}',
            '{{%stat_bigrun_distrib_abstract3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_bigrun_distrib3}}',
            '{{%stat_bigrun_distrib_abstract3}}',
        ];
    }
}
