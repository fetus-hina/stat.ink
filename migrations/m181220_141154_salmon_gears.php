<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m181220_141154_salmon_gears extends Migration
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
                static::name2key('Beekeeper Hat'),
                'Beekeeper Hat',
                'headgear',
                'grizzco',
                null,
                21003,
            ],
            [
                static::name2key('Wooden Sandals'),
                'Wooden Sandals',
                'shoes',
                'grizzco',
                null,
                21006,
            ],
        ];
    }
}
