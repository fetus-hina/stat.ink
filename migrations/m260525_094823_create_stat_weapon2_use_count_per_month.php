<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m260525_094823_create_stat_weapon2_use_count_per_month extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%stat_weapon2_use_count_per_month}}', [
            'year_month' => $this->integer()->notNull(),
            'rule_id' => $this->pkRef('{{%rule2}}'),
            'weapon_id' => $this->pkRef('{{%weapon2}}'),
            'map_id' => $this->pkRef('{{%map2}}'),
            'battles' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),
            'kills' => $this->bigInteger()->notNull(),
            'deaths' => $this->bigInteger()->notNull(),
            'kd_available' => $this->bigInteger()->notNull(),
            'kills_with_time' => $this->bigInteger()->notNull(),
            'deaths_with_time' => $this->bigInteger()->notNull(),
            'kd_time_available' => $this->bigInteger()->notNull(),
            'kd_time_seconds' => $this->bigInteger()->notNull(),
            'specials' => $this->bigInteger()->notNull(),
            'specials_available' => $this->bigInteger()->notNull(),
            'specials_with_time' => $this->bigInteger()->notNull(),
            'specials_time_available' => $this->bigInteger()->notNull(),
            'specials_time_seconds' => $this->bigInteger()->notNull(),
            'inked' => $this->bigInteger()->notNull(),
            'inked_available' => $this->bigInteger()->notNull(),
            'inked_with_time' => $this->bigInteger()->notNull(),
            'inked_time_available' => $this->bigInteger()->notNull(),
            'inked_time_seconds' => $this->bigInteger()->notNull(),
            'knockout_wins' => $this->bigInteger()->null(),
            'timeup_wins' => $this->bigInteger()->null(),
            'knockout_loses' => $this->bigInteger()->null(),
            'timeup_loses' => $this->bigInteger()->null(),
            $this->tablePrimaryKey([
                'year_month',
                'rule_id',
                'weapon_id',
                'map_id',
            ]),
        ]);

        $sumColumns = [
            'battles',
            'wins',
            'kills',
            'deaths',
            'kd_available',
            'kills_with_time',
            'deaths_with_time',
            'kd_time_available',
            'kd_time_seconds',
            'specials',
            'specials_available',
            'specials_with_time',
            'specials_time_available',
            'specials_time_seconds',
            'inked',
            'inked_available',
            'inked_with_time',
            'inked_time_available',
            'inked_time_seconds',
            'knockout_wins',
            'timeup_wins',
            'knockout_loses',
            'timeup_loses',
        ];

        $columns = array_merge(
            ['year_month', 'rule_id', 'weapon_id', 'map_id'],
            $sumColumns,
        );

        $selects = array_merge(
            [
                "TO_CHAR(period2_to_timestamp([[period]]) AT TIME ZONE 'UTC', 'YYYYMM')::integer",
                '[[rule_id]]',
                '[[weapon_id]]',
                '[[map_id]]',
            ],
            array_map(
                fn (string $col): string => "SUM([[{$col}]])",
                $sumColumns,
            ),
        );

        $this->execute(vsprintf('INSERT INTO %s ( %s ) SELECT %s FROM %s GROUP BY %s', [
            $this->db->quoteTableName('{{%stat_weapon2_use_count_per_month}}'),
            implode(', ', array_map(
                fn (string $c): string => $this->db->quoteColumnName($c),
                $columns,
            )),
            implode(', ', $selects),
            $this->db->quoteTableName('{{%stat_weapon2_use_count}}'),
            implode(', ', ['1', '[[rule_id]]', '[[weapon_id]]', '[[map_id]]']),
        ]));
    }

    public function safeDown()
    {
        $this->dropTable('{{%stat_weapon2_use_count_per_month}}');
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%stat_weapon2_use_count_per_month}}',
        ];
    }
}
