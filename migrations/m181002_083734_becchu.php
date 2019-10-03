<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m181002_083734_becchu extends Migration
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
            ['prime_becchu', 'Kensa Splattershot Pro', 'shooter', 'splashbomb', 'nicedama', 'prime', null, 72],
            ['nova_becchu', 'Kensa Luna Blaster', 'blaster', 'tansanbomb', 'amefurashi', 'nova', null, 202],
            ['dynamo_becchu', 'Kensa Dynamo Roller', 'roller', 'sprinkler', 'nicedama', 'dynamo', null, 1022],
            ['screwslosher_becchu', 'Kensa Sloshing Machine', 'slosher', 'tansanbomb', 'chakuchi', 'screwslosher', null, 3022],
        ];
    }
}
