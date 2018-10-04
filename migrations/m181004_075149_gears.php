<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m181004_075149_gears extends Migration
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
            static::salmonGear2('North-Country Parka', 'clothing', 21005),
            static::salmonGear2('Worker\'s Head Towel', 'headgear', 21007),

            // Nintendo Switch Online
            [
                static::name2key('Online Squidkid V'),
                'Online Squidkid V',
                'shoes',
                'enperry',
                'stealth_jump',
                2041,
            ],
            [
                static::name2key('Online Jersey'),
                'Online Jersey',
                'clothing',
                'grizzco',
                'swim_speed_up',
                8029,
            ],

            // Halloween Splatfest
            [
                static::name2key('Kyonshi Hat'),
                'Kyonshi Hat',
                'headgear',
                'enperry', // tmp
                null,
                24000,
            ],
            [
                static::name2key('Li\'l Devil Horns'),
                'Li\'l Devil Horns',
                'headgear',
                'enperry', // tmp
                null,
                24001,
            ],
            [
                static::name2key('Hockey Mask'),
                'Hockey Mask',
                'headgear',
                'enperry', // tmp
                null,
                24002,
            ],
            [
                static::name2key('Anglerfish Mask'),
                'Anglerfish Mask',
                'headgear',
                'enperry', // tmp
                null,
                24003,
            ],
        ];
    }
}
