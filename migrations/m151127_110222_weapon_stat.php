<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151127_110222_weapon_stat extends Migration
{
    public function up()
    {
        $this->createTable('stat_weapon', [
            'rule_id' => $this->integer()->notNull(),
            'weapon_id' => $this->integer()->notNull(),
            'players' => $this->bigInteger()->notNull(),
            'total_kill' => $this->bigInteger()->notNull(),
            'total_death' => $this->bigInteger()->notNull(),
            'win_count' => $this->bigInteger()->notNull(),
            'total_point' => $this->bigInteger(),
            'point_available' => $this->bigInteger(),
        ]);
        $this->addPrimaryKey('pk_stat_weapon', 'stat_weapon', ['rule_id', 'weapon_id']);
        $this->addForeignKey('fk_stat_weapon_1', 'stat_weapon', 'rule_id', 'rule', 'id');
        $this->addForeignKey('fk_stat_weapon_2', 'stat_weapon', 'weapon_id', 'weapon', 'id');

        $this->createTable('stat_weapon_battle_count', [
            'rule_id' => $this->primaryKey(),
            'count' => $this->bigInteger()->notNull(),
        ]);
        $this->addForeignKey('fk_stat_weapon_battle_count_1', 'stat_weapon_battle_count', 'rule_id', 'rule', 'id');
    }

    public function down()
    {
        $this->dropTable('stat_weapon_battle_count');
        $this->dropTable('stat_weapon');
    }
}
