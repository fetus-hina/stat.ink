<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180207_113118_weapon2_use_count_update extends Migration
{
    public function up()
    {
        $this->execute('TRUNCATE TABLE {{stat_weapon2_use_count}}');
        $this->execute('TRUNCATE TABLE {{stat_weapon2_use_count_per_week}}');
        $this->execute('ALTER TABLE {{stat_weapon2_use_count}} ' . implode(', ', [
            'ADD COLUMN [[map_id]] INTEGER NOT NULL REFERENCES {{map2}}([[id]])',
            'DROP CONSTRAINT stat_weapon2_use_count_pkey',
            'ADD CONSTRAINT stat_weapon2_use_count_pkey ' .
                'PRIMARY KEY ([[period]], [[rule_id]], [[weapon_id]], [[map_id]])',
        ]));
        $this->execute('ALTER TABLE {{stat_weapon2_use_count_per_week}} ' . implode(', ', [
            'ADD COLUMN [[map_id]] INTEGER NOT NULL REFERENCES {{map2}}([[id]])',
            'DROP CONSTRAINT stat_weapon2_use_count_per_week_pkey',
            'ADD CONSTRAINT stat_weapon2_use_count_per_week_pkey ' .
                'PRIMARY KEY ([[isoyear]], [[isoweek]], [[rule_id]], [[weapon_id]], [[map_id]])',
        ]));
    }

    public function down()
    {
        $this->execute('TRUNCATE TABLE {{stat_weapon2_use_count}}');
        $this->execute('TRUNCATE TABLE {{stat_weapon2_use_count_per_week}}');
        $this->execute('ALTER TABLE {{stat_weapon2_use_count}} ' . implode(', ', [
            'DROP CONSTRAINT stat_weapon2_use_count_pkey',
            'ADD CONSTRAINT stat_weapon2_use_count_pkey PRIMARY KEY ([[period]], [[rule_id]], [[weapon_id]])',
            'DROP COLUMN [[map_id]]',
        ]));
        $this->execute('ALTER TABLE {{stat_weapon2_use_count_per_week}} ' . implode(', ', [
            'DROP CONSTRAINT stat_weapon2_use_count_per_week_pkey',
            'ADD CONSTRAINT stat_weapon2_use_count_per_week_pkey ' .
                'PRIMARY KEY ([[isoyear]], [[isoweek]], [[rule_id]], [[weapon_id]])',
            'DROP COLUMN [[map_id]]',
        ]));
    }
}
