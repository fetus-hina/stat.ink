<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160127_092116_kdwin_stat extends Migration
{
    public function up()
    {
        $this->createTable('stat_weapon_kd_win_rate', [
            'rule_id' => $this->integer()->notNull(),
            'map_id' => $this->integer()->notNull(),
            'weapon_id' => $this->integer()->notNull(),
            'kill' => $this->integer()->notNull(),
            'death' => $this->integer()->notNull(),
            'battle_count' => $this->bigInteger()->notNull(),
            'win_count' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey(
            'pk_stat_weapon_kd_win_rate',
            'stat_weapon_kd_win_rate',
            ['rule_id', 'map_id', 'weapon_id', 'kill', 'death'],
        );
        $this->addForeignKey('fk_stat_weapon_kd_win_rate_1', 'stat_weapon_kd_win_rate', 'rule_id', 'rule', 'id');
        $this->addForeignKey('fk_stat_weapon_kd_win_rate_2', 'stat_weapon_kd_win_rate', 'map_id', 'map', 'id');
        $this->addForeignKey('fk_stat_weapon_kd_win_rate_3', 'stat_weapon_kd_win_rate', 'weapon_id', 'weapon', 'id');
    }

    public function down()
    {
        $this->dropTable('stat_weapon_kd_win_rate');
    }
}
