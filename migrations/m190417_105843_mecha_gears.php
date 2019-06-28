<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m190417_105843_mecha_gears extends Migration
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
                static::name2key('Mecha Head - HTR'),
                'Mecha Head - HTR',
                'headgear',
                'squidforce',
                'main_power_up',
                22000,
            ],
            [
                static::name2key('Mecha Body - AKM'),
                'Mecha Body - AKM',
                'clothing',
                'squidforce',
                'sub_power_up',
                22000,
            ],
            [
                static::name2key('Mecha Legs - LBS'),
                'Mecha Legs - LBS',
                'shoes',
                'squidforce',
                'object_shredder',
                22000,
            ],
        ];
    }
}
