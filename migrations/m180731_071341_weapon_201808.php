<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180731_071341_weapon_201808 extends Migration
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

    public function getWeapons(): array
    {
        return [
            [
                'quadhopper_white',
                'Light Tetra Dualies',
                'maneuver',
                'sprinkler',
                'pitcher',
                'quadhopper_black',
                null,
                5041,
            ],
            ['hydra_custom', 'Custom Hydra Splatling', 'splatling', 'trap', 'armor', 'hydra', null, 4021],
            ['furo', 'Bloblobber', 'slosher', 'splashshield', 'amefurashi', null, null, 3030],
            ['nautilus47', 'Nautilus 47', 'splatling', 'pointsensor', 'sphere', null, null, 4040],
        ];
    }
}
