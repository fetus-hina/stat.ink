<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m190205_101752_gears extends Migration
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
                static::name2key('Koshien Bandana'),
                'Koshien Bandana',
                'headgear',
                'squidforce',
                'swim_speed_up',
                8013,
            ],
            [
                static::name2key('Octo Support Hoodie'),
                'Octo Support Hoodie',
                'clothing',
                'squidforce',
                'main_power_up',
                10011,
            ],
            [
                static::name2key('Office Attire'),
                'Office Attire',
                'clothing',
                'grizzco',
                null,
                21009,
            ],
        ];
    }
}
