<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180608_184513_swc_logo_tee extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            static::name2key('SWC Logo Tee'),
            'SWC Logo Tee',
            'clothing',
            static::name2key('SquidForce'),
            'swim_speed_up',
            1061,
        );
    }

    public function safeDown()
    {
        $this->downGear2(
            static::name2key('SWC Logo Tee'),
        );
    }
}
