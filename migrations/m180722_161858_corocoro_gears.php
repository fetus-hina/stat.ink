<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180722_161858_corocoro_gears extends Migration
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
                static::name2key('Sennyu Bon Bon Beanie'),
                'Sennyu Bon Bon Beanie',
                'headgear',
                'enperry',
                'ink_saver_sub',
                2006,
            ],
            [
                static::name2key('Sennyu Goggles'),
                'Sennyu Goggles',
                'headgear',
                'enperry',
                'ink_resistance_up',
                3019,
            ],
            [
                static::name2key('Sennyu Specs'),
                'Sennyu Specs',
                'headgear',
                'enperry',
                'swim_speed_up',
                3020,
            ],
            [
                static::name2key('Sennyu Headphones'),
                'Sennyu Headphones',
                'headgear',
                'enperry',
                'ink_saver_main',
                5006,
            ],
            [
                static::name2key('Sennyu Suit'),
                'Sennyu Suit',
                'clothing',
                'enperry',
                'squid_ninja',
                5044,
            ],
            [
                static::name2key('Sennyu Inksoles'),
                'Sennyu Inksoles',
                'shoes',
                'enperry',
                'stealth_jump',
                8012,
            ],
        ];
    }
}
