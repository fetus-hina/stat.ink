<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m181220_135153_frosty_gear extends Migration
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
                static::name2key('Festive Party Cone'),
                'Festive Party Cone',
                'headgear',
                'enperry',
                null,
                24004,
            ],
            [
                static::name2key("New Year's Glasses DX"),
                "New Year's Glasses DX",
                'headgear',
                'enperry',
                null,
                24005,
            ],
            [
                static::name2key('Twisty Headband'),
                'Twisty Headband',
                'headgear',
                'enperry',
                null,
                24006,
            ],
            [
                static::name2key('Eel-Cake Hat'),
                'Eel-Cake Hat',
                'headgear',
                'enperry',
                null,
                24007,
            ],
        ];
    }
}
