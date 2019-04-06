<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m190405_111334_user_rule_stats extends Migration
{
    public function safeUp()
    {
        $stats = function (string $baseName): array {
            return [
                "avg_{$baseName}" => $this->double(),
                "min_{$baseName}" => $this->integer(),
                "pct5_{$baseName}" => $this->double(),
                "q1_4_{$baseName}" => $this->double(),
                "median_{$baseName}" => $this->double(),
                "q3_4_{$baseName}" => $this->double(),
                "pct95_{$baseName}" => $this->double(),
                "max_{$baseName}" => $this->integer(),
                "stddev_{$baseName}" => $this->double(),
                "{$baseName}_per_min" =>$this->double(),
            ];
        };

        $this->createTable('user_rule_stat2', array_merge(
            [
                'user_id' => $this->pkRef('user')->notNull(),
                'mode_id' => $this->pkRef('mode2')->notNull(),
                'rule_id' => $this->pkRef('rule2')->notNull(),
                'battles' => $this->bigInteger()->notNull()->defaultValue(0),
                'have_win_lose' => $this->bigInteger()->notNull()->defaultValue(0),
                'battles_win' => $this->bigInteger()->notNull()->defaultValue(0),
            ],
            $stats('kill'),
            $stats('death'),
            $stats('assist'),
            $stats('inked'),
            $stats('power'),
            [
                'rank_peak' => $this->integer()->null(),
                'rank_current' => $this->integer()->null(),
                'power_current' => $this->integer()->null(),
                'updated_at' => $this->timestampTZ()->notNull(),
                'PRIMARY KEY ([[user_id]], [[mode_id]], [[rule_id]])',
            ],
        ));
    }

    public function safeDown()
    {
        $this->dropTable('user_rule_stat2');
    }
}
