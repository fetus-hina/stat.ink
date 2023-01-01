<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171103_103422_ability2 extends Migration
{
    public function up()
    {
        $this->createTable('ability2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(32),
            'name' => $this->string(32)->notNull(),
        ]);
        $this->batchInsert('ability2', ['key', 'name'], [
            ['ability_doubler', 'Ability Doubler'],
            ['bomb_defense_up', 'Bomb Defense Up'],
            ['cold_blooded', 'Cold-Blooded'],
            ['comeback', 'Comeback'],
            ['drop_roller', 'Drop Roller'],
            ['haunt', 'Haunt'],
            ['ink_recovery_up', 'Ink Recovery Up'],
            ['ink_resistance_up', 'Ink Resistance Up'],
            ['ink_saver_main', 'Ink Saver (Main)'],
            ['ink_saver_sub', 'Ink Saver (Sub)'],
            ['last_ditch_effort', 'Last-Ditch Effort'],
            ['ninja_squid', 'Ninja Squid'],
            ['object_shredder', 'Object Shredder'],
            ['opening_gambit', 'Opening Gambit'],
            ['quick_respawn', 'Quick Respawn'],
            ['quick_super_jump', 'Quick Super Jump'],
            ['respawn_punisher', 'Respawn Punisher'],
            ['run_speed_up', 'Run Speed Up'],
            ['special_charge_up', 'Special Charge Up'],
            ['special_power_up', 'Special Power Up'],
            ['special_saver', 'Special Saver'],
            ['stealth_jump', 'Stealth Jump'],
            ['sub_power_up', 'Sub Power Up'],
            ['swim_speed_up', 'Swim Speed Up'],
            ['tenacity', 'Tenacity'],
            ['thermal_ink', 'Thermal Ink'],
        ]);
        $this->analyze('ability2');
    }

    public function down()
    {
        $this->dropTable('ability2');
    }
}
