<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m181126_075735_dec_weapons extends Migration
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
            ['explosher_custom', 'Custom Explosher', 'slosher', 'pointsensor', 'sphere', 'explosher', null, 3041],
            ['kugelschreiber_hue', 'Ballpoint Splatling Nouveau', 'splatling', 'jumpbeacon', 'amefurashi', 'kugelschreiber', null, 4031],
            ['furo_deco', 'Bloblobber Deco', 'slosher', 'sprinkler', 'pitcher', 'furo', null, 3031],
            ['nautilus79', 'Nautilus 79', 'splatling', 'kyubanbomb', 'jetpack', 'nautilus47', null, 4041],
        ];
    }
}
