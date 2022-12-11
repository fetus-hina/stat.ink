<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

final class m221211_091429_bigrun_stats extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_stat_bigrun3}}', [
            'user_id' => $this->pkRef('{{%user}}')->notNull(),
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'golden_eggs' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[user_id]], [[schedule_id]])',
        ]);

        $db = $this->db;
        assert($db instanceof Connection);
        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%user_stat_bigrun3}}'),
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
                            '{{%salmon3}}.[[is_big_run]]' => true,
                            '{{%salmon3}}.[[is_deleted]]' => false,
                            '{{%salmon3}}.[[is_private]]' => false,
                        ],
                        ['not', ['{{%salmon3}}.[[golden_eggs]]' => null]],
                        ['not', ['{{%salmon_schedule3}}.[[big_map_id]]' => null]],
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

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_stat_bigrun3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%user_stat_bigrun3}}',
        ];
    }
}
