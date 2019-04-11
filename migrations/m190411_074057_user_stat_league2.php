<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m190411_074057_user_stat_league2 extends Migration
{
    public function safeUp()
    {
        $this->createTable('user_stat_league2', array_merge(
            [
                'user_id'   => $this->pkRef('user')->notNull(),
                'battles'   => $this->bigInteger()->notNull()->defaultValue(0),
                'win_ko'    => $this->bigInteger()->notNull()->defaultValue(0),
                'lose_ko'   => $this->bigInteger()->notNUll()->defaultValue(0),
                'win_time'  => $this->bigInteger()->notNull()->defaultValue(0),
                'lose_time' => $this->bigInteger()->notNull()->defaultValue(0),
                'win_unk'   => $this->bigInteger()->notNull()->defaultValue(0),
                'lose_unk'  => $this->bigInteger()->notNull()->defaultValue(0),
            ],
            $this->statColumns('kill'),
            $this->statColumns('death'),
            $this->statColumns('assist'),
            [
                'updated_at' => $this->timestampTZ(0)->notNull(),
                'PRIMARY KEY ([[user_id]])',
            ],
        ));
    }

    public function safeDown()
    {
        $this->dropTable('user_stat_league2');
    }

    private function statColumns(string $baseName): array
    {
        $bigint = $this->bigInteger()->null();
        $int = $this->integer()->null();
        $number = $this->double()->null();
        return [
            "have_{$baseName}" => $bigint,
            "total_{$baseName}" => $bigint,
            "total_{$baseName}_with_time" => $bigint,
            "total_time_{$baseName}" => $bigint,
            "min_{$baseName}" => $int,
            "pct5_{$baseName}" => $number,
            "q1_4_{$baseName}" => $number,
            "median_{$baseName}" => $number,
            "q3_4_{$baseName}" => $number,
            "pct95_{$baseName}" => $number,
            "max_{$baseName}" => $int,
            "stddev_{$baseName}" => $number,
        ];
    }
}
