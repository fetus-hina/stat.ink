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
            // {{{
            $keys = [
                "have_{$baseName}",
                "total_{$baseName}",
                "seconds_{$baseName}",
                "min_{$baseName}",
                "pct5_{$baseName}",
                "q1_4_{$baseName}",
                "median_{$baseName}",
                "q3_4_{$baseName}",
                "pct95_{$baseName}",
                "max_{$baseName}",
            ];
            return array_merge(
                array_combine(
                    $keys,
                    array_fill(
                        0,
                        count($keys),
                        $this->bigInteger()->null(),
                    )
                ),
                [
                    "stddev_{$baseName}" => $this->double()->null(),
                ],
            );
            // }}}
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
            [
                'rank_peak' => $this->integer()->null(),
                'rank_current' => $this->integer()->null(),
                'updated_at' => $this->timestampTZ()->notNull(),
            ],
            [
                'PRIMARY KEY ([[user_id]], [[mode_id]], [[rule_id]])',
            ],
        ));
    }

    public function safeDown()
    {
        $this->dropTable('user_rule_stat2');
    }
}
