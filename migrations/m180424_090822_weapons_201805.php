<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180424_090822_weapons_201805 extends Migration
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
            ['sharp_neo', 'Neo Splash-o-matic', 'shooter', 'quickbomb', 'pitcher', 'sharp', null, 21],
            ['bottlegeyser_foil', 'Foil Squeezer', 'shooter', 'splashbomb', 'bubble', 'bottlegeyser', null, 401],
            ['squiclean_b', 'New Squiffer', 'charger', 'robotbomb', 'sphere', 'squiclean_a', null, 2001],
            ['kelvin525_deco', 'Glooga Dualies Deco', 'maneuver', 'splashshield', 'sphere', 'kelvin525', null, 5021],
        ];
    }
}
