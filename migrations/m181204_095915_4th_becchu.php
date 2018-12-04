<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m181204_095915_4th_becchu extends Migration
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
            ['52gal_becchu', 'Kensa .52 Gal', 'shooter', 'splashshield', 'nicedama', '52gal', null, 52],
            ['kelvin525_becchu', 'Kensa Glooga Dualies', 'maneuver', 'tansanbomb', 'armor', 'kelvin525', null, 5022],
            ['splatspinner_becchu', 'Kensa Mini Splatling', 'splatling', 'poisonmist', 'ultrahanko', 'splatspinner', null, 4002],
            ['rapid_becchu', 'Kensa Rapid Blaster', 'blaster', 'torpedo', 'sphere', 'rapid', null, 242],
        ];
    }
}
