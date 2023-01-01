<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m161226_112257_stat_weapon_map_trend extends Migration
{
    public function up()
    {
        $this->createTable('stat_weapon_map_trend', [
            'rule_id' => 'INTEGER NOT NULL REFERENCES {{rule}}([[id]])',
            'map_id' => 'INTEGER NOT NULL REFERENCES {{map}}([[id]])',
            'weapon_id' => 'INTEGER NOT NULL REFERENCES {{weapon}}([[id]])',
            'battles' => 'BIGINT NOT NULL',
        ]);
        $this->addPrimaryKey('pk_stat_weapon_map_trend', 'stat_weapon_map_trend', ['rule_id', 'map_id', 'weapon_id']);
        $this->createIndex('ix_stat_weapon_map_trend_1', 'stat_weapon_map_trend', ['rule_id', 'map_id', 'battles']);
    }

    public function down()
    {
        $this->dropTable('stat_weapon_map_trend');
    }
}
