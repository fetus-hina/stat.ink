<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160704_123222_stat_weapon_vs_weapon extends Migration
{
    public function up()
    {
        $this->execute(sprintf(
            'CREATE TABLE {{stat_weapon_vs_weapon}} ( %s )',
            implode(', ', [
                '[[version_id]] INTEGER NOT NULL REFERENCES {{splatoon_version}}([[id]])',
                '[[rule_id]] INTEGER NOT NULL REFERENCES {{rule}}([[id]])',
                '[[weapon_id_1]] INTEGER NOT NULL REFERENCES {{weapon}}([[id]])',
                '[[weapon_id_2]] INTEGER NOT NULL REFERENCES {{weapon}}([[id]])',
                '[[battle_count]] BIGINT NOT NULL CHECK ([[battle_count]] > 0)',
                '[[win_count]] BIGINT NOT NULL CHECK ([[win_count]] >= 0)',
                'CHECK ([[battle_count]] >= [[win_count]])',
                'CHECK ([[weapon_id_1]] < [[weapon_id_2]])',
                'PRIMARY KEY ([[version_id]], [[rule_id]], [[weapon_id_1]], [[weapon_id_2]])',
            ]),
        ));
    }

    public function down()
    {
        $this->dropTable('stat_weapon_vs_weapon');
    }
}
