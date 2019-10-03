<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180914_064925_becchu_weapons extends Migration
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
            ['sshooter_becchu', 'Kensa Splattershot', 'shooter', 'kyubanbomb', 'missile', 'sshooter', null, 42],
            ['splatroller_becchu', 'Kensa Splat Roller', 'roller', 'splashbomb', 'bubble', 'splatroller', null, 1012],
            ['splatcharger_becchu', 'Kensa Charger', 'charger', 'sprinkler', 'sphere', 'splatcharger', null, 2012],
            ['splatscope_becchu', 'Kensa Splatterscope', 'charger', 'sprinkler', 'sphere', 'splatcharger', null, 2022],
            ['maneuver_becchu', 'Kensa Splat Dualies', 'maneuver', 'kyubanbomb', 'sphere', 'maneuver', null, 5012],
        ];
    }
}
