<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180616_120011_oldtimyclothes extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            static::name2key('Old-Timey Clothes'),
            'Old-Timey Clothes',
            'clothing',
            static::name2key('Cuttlegear'),
            static::name2key('Thermal Ink'),
            27106,
        );
    }

    public function safeDown()
    {
        $this->downGear2(static::name2key('Old-Timey Clothes'));
    }
}
