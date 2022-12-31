<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180601_173510_salmon_201806 extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            static::name2key('Cap of Legend'),
            'Cap of Legend',
            'headgear',
            static::name2key('Grizzco'),
            null,
            21005,
        );
    }

    public function safeDown()
    {
        $this->downGear2(
            static::name2key('Cap of Legend'),
        );
    }
}
