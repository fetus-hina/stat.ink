<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151227_082610_ability extends Migration
{
    public function up()
    {
        $this->createTable('ability', [
            'id'    => $this->primaryKey(),
            'key'   => 'VARCHAR(32) NOT NULL UNIQUE',
            'name'  => 'VARCHAR(32) NOT NULL',
        ]);
        $this->batchInsert('ability', ['key', 'name'], [
            [ 'bomb_range_up',          'Bomb Range Up' ],
            [ 'bomb_sniffer',           'Bomb Sniffer' ],
            [ 'cold_blooded',           'Cold-Blooded' ],
            [ 'comeback',               'Comeback' ],
            [ 'damage_up',              'Damage Up' ],
            [ 'defense_up',             'Defense Up' ],
            [ 'haunt',                  'Haunt' ],
            [ 'ink_recovery_up',        'Ink Recovery Up' ],
            [ 'ink_resistance_up',      'Ink Resistance Up' ],
            [ 'ink_saver_main',         'Ink Saver (Main)' ],
            [ 'ink_saver_sub',          'Ink Saver (Sub)' ],
            [ 'last_ditch_effort',      'Last-Ditch Effort' ],
            [ 'ninja_squid',            'Ninja Squid' ],
            [ 'opening_gambit',         'Opening Gambit' ],
            [ 'quick_respawn',          'Quick Respawn' ],
            [ 'quick_super_jump',       'Quick Super Jump' ],
            [ 'recon',                  'Recon' ],
            [ 'run_speed_up',           'Run Speed Up' ],
            [ 'special_charge_up',      'Special Charge Up' ],
            [ 'special_duration_up',    'Special Duration Up' ],
            [ 'special_saver',          'Special Saver' ],
            [ 'stealth_jump',           'Stealth Jump' ],
            [ 'swim_speed_up',          'Swim Speed Up' ],
            [ 'tenacity',               'Tenacity' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('ability');
    }
}
