<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160910_083818_stat_weapon_use_count extends Migration
{
    public function up()
    {
        $this->execute('CREATE TABLE {{stat_weapon_use_count}} (' . implode(', ', [
            '[[period]] INTEGER NOT NULL',
            '[[rule_id]] INTEGER NOT NULL REFERENCES {{rule}}([[id]])',
            '[[weapon_id]] INTEGER NOT NULL REFERENCES {{weapon}}([[id]])',
            '[[battles]] BIGINT NOT NULL',
            '[[wins]] BIGINT NOT NULL',
            'PRIMARY KEY ( [[period]], [[rule_id]], [[weapon_id]] )',
        ]) . ')');
    }

    public function down()
    {
        $this->dropTable('stat_weapon_use_count');
    }
}
