<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180713_193549_off_the_hook_gear extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        foreach ($this->getGears() as $gearData) {
            call_user_func_array([$this, 'upGear2'], $gearData);
        }
    }

    public function safeDown()
    {
        foreach ($this->getGears() as $gearData) {
            $this->downGear2($gearData[0]);
        }
    }

    public function getGears(): array
    {
        return [
            [
                static::name2key('Pearlescent Crown'),
                'Pearlescent Crown',
                'headgear',
                'amiibo',
                'cold_blooded',
                25006,
            ],
            [
                static::name2key('Marinated Headphones'),
                'Marinated Headphones',
                'headgear',
                'amiibo',
                'special_saver',
                25007,
            ],
            [
                static::name2key('Pearlescent Hoodie'),
                'Pearlescent Hoodie',
                'clothing',
                'amiibo',
                'respawn_punisher',
                25006,
            ],
            [
                static::name2key('Marinated Top'),
                'Marinated Top',
                'clothing',
                'amiibo',
                'special_power_up',
                25007,
            ],
            [
                static::name2key('Pearlescent Kicks'),
                'Pearlescent Kicks',
                'shoes',
                'amiibo',
                'special_charge_up',
                25006,
            ],
            [
                static::name2key('Marinated Slip-Ons'),
                'Marinated Slip-Ons',
                'shoes',
                'amiibo',
                'ink_recovery_up',
                25007,
            ],
        ];
    }
}
