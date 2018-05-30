<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180530_175832_weapons_201806 extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        foreach ($this->getWeapons() as $weaponData) {
            call_user_func_array([$this, 'upWeapon'], $weaponData);
        }
    }

    public function safeDown()
    {
        foreach ($this->getWeapons() as $weaponData) {
            $this->downWeapon($weaponData[0]);
        }
    }

    public function getWeapons() : array
    {
        return [
            ['dualsweeper_custom', 'Custom Dualie Squelchers', 'maneuver', 'splashbomb', 'amefurashi', 'dualsweeper', null, 5031],
            ['rapid_elite_deco', 'Rapid Blaster Pro Deco', 'blaster', 'splashshield', 'armor', 'rapid_elite', null, 251],
            ['spygadget_sorella', 'Undercover Sorella Brella', 'brella', 'splashbomb', 'sphere', 'spygadget', null, 6021],
            ['carbon_deco', 'Carbon Roller Deco', 'roller', 'quickbomb', 'pitcher', 'carbon', null, 1001],
        ];
    }
}
