<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160117_123500_weapon_kd extends Migration
{
    public function up()
    {
        $this->createTable('stat_weapon_kill_death', [
            'weapon_id' => $this->integer()->notNull(),
            'rule_id' => $this->integer()->notNull(),
            'kill' => $this->integer()->notNull(),
            'death' => $this->integer()->notNull(),
            'battle' => $this->bigInteger()->notNull(),
            'win' => $this->bigInteger()->notNull(),
        ]);
        $this->addPrimaryKey('pk_stat_weapon_kill_death', 'stat_weapon_kill_death', [
            'weapon_id',
            'rule_id',
            'kill',
            'death',
        ]);
        $this->addForeignKey('fk_stat_weapon_kill_death_1', 'stat_weapon_kill_death', 'weapon_id', 'weapon', 'id');
        $this->addForeignKey('fk_stat_weapon_kill_death_2', 'stat_weapon_kill_death', 'rule_id', 'rule', 'id');
    }

    public function down()
    {
        $this->dropTable('stat_weapon_kill_death');
    }
}
