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

final class m230410_032051_eggstra_stats extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_stat_eggstra_work3}}', [
            'user_id' => $this->pkRef('{{%user}}')->notNull(),
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'golden_eggs' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[user_id]], [[schedule_id]])',
        ]);

        $this->createTable('{{%stat_eggstra_work_distrib3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'golden_egg' => $this->integer()->notNull(),
            'users' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]], [[golden_egg]])',
        ]);

        $this->createTable('{{%stat_eggstra_work_distrib_abstract3}}', [
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

        $this->createTable('{{%eggstra_work_official_result3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'gold' => $this->bigInteger()->notNull(),
            'silver' => $this->bigInteger()->notNull(),
            'bronze' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]])',
        ]);

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%user_stat_eggstra_work3}}'),
                implode(', ', array_map(
                    fn (string $column): string => $db->quoteColumnName($column),
                    ['user_id', 'schedule_id', 'golden_eggs'],
                )),
                (new Query())
                    ->select([
                        'user_id' => '{{%salmon3}}.[[user_id]]',
                        'schedule_id' => '{{%salmon3}}.[[schedule_id]]',
                        'golden_eggs' => 'MAX({{%salmon3}}.[[golden_eggs]])',
                    ])
                    ->from('{{%salmon3}}')
                    ->innerJoin('{{%salmon_schedule3}}', '{{%salmon3}}.[[schedule_id]] = {{%salmon_schedule3}}.[[id]]')
                    ->andWhere(['and',
                        [
                            '{{%salmon3}}.[[is_automated]]' => true,
                            '{{%salmon3}}.[[is_deleted]]' => false,
                            '{{%salmon3}}.[[is_eggstra_work]]' => true,
                            '{{%salmon3}}.[[is_private]]' => false,
                            '{{%salmon_schedule3}}.[[is_eggstra_work]]' => true,
                        ],
                        ['not', ['{{%salmon3}}.[[golden_eggs]]' => null]],
                        ['>=', '{{%salmon3}}.[[golden_eggs]]', 0],
                    ])
                    ->groupBy([
                        '{{%salmon3}}.[[user_id]]',
                        '{{%salmon3}}.[[schedule_id]]',
                    ])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_eggstra_work_distrib3}}'),
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
                    ->from('{{%user_stat_eggstra_work3}}')
                    ->groupBy([
                        'schedule_id',
                        'TRUNC([[golden_eggs]] / 5) * 5',
                    ])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        $percentile = fn (float $pos): string => sprintf(
            'PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY [[golden_eggs]] DESC)',
            $pos,
        );

        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_eggstra_work_distrib_abstract3}}'),
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
                    ->from('{{%user_stat_eggstra_work3}}')
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
            '{{%eggstra_work_official_result3}}',
            '{{%stat_eggstra_work_distrib_abstract3}}',
            '{{%stat_eggstra_work_distrib3}}',
            '{{%user_stat_eggstra_work3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%eggstra_work_official_result3}}',
            '{{%stat_eggstra_work_distrib3}}',
            '{{%stat_eggstra_work_distrib_abstract3}}',
            '{{%user_stat_eggstra_work3}}',
        ];
    }
}
