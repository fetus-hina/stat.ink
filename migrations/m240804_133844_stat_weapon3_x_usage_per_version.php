<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\ColumnSchema;

final class m240804_133844_stat_weapon3_x_usage_per_version extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            '{{%stat_weapon3_x_usage_per_version}}',
            array_merge(
                [
                    'version_group_id' => $this->pkRef('{{%splatoon_version_group3}}')->notNull(),
                    'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
                    'range_id' => $this->pkRef('{{%stat_weapon3_x_usage_range}}')->notNull(),
                    'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
                    'battles' => $this->bigInteger()->notNull(),
                    'wins' => $this->bigInteger()->notNull(),
                    'seconds' => $this->bigInteger()->notNull(),
                ],
                $this->makeStatsColumns('kill'),
                $this->makeStatsColumns('assist'),
                $this->makeStatsColumns('death'),
                $this->makeStatsColumns('special'),
                $this->makeStatsColumns('inked', 5),
                [
                    'PRIMARY KEY ([[version_group_id]], [[rule_id]], [[range_id]], [[weapon_id]])',
                ],
            ),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_weapon3_x_usage_per_version}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_weapon3_x_usage_per_version}}',
        ];
    }

    /**
     * @return array<string, ColumnSchema>
     */
    private function makeStatsColumns(string $name, int $precision = 3): array
    {
        return [
            "avg_{$name}" => $this->double()->notNull(),
            "sd_{$name}" => $this->double()->null(),
            "min_{$name}" => $this->integer()->notNull(),
            "p05_{$name}" => $this->decimal($precision, 1)->null(),
            "p25_{$name}" => $this->decimal($precision, 1)->null(),
            "p50_{$name}" => $this->decimal($precision, 1)->null(),
            "p75_{$name}" => $this->decimal($precision, 1)->null(),
            "p95_{$name}" => $this->decimal($precision, 1)->null(),
            "max_{$name}" => $this->integer()->notNull(),
            "mode_{$name}" => $this->integer()->null(),
        ];
    }
}
