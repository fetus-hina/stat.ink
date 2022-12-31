<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m171215_094600_corocoro_gears extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            $this->name2key('Imperial Kaiser'),
            'Imperial Kaiser',
            'shoes',
            'enperry',
            'swim_speed_up',
            2021,
        );
        $this->upGear2(
            $this->name2key('Kaiser Cuff'),
            'Kaiser Cuff',
            'headgear',
            'enperry',
            'ink_saver_main',
            10000,
        );
        $this->upGear2(
            $this->name2key('King Bench Kaiser'),
            'King Bench Kaiser',
            'clothing',
            'enperry',
            'run_speed_up',
            5032,
        );
    }

    public function safeDown()
    {
        $list = [
            $this->name2key('Imperial Kaiser'),
            $this->name2key('Kaiser Cuff'),
            $this->name2key('King Bench Kaiser'),
        ];
        foreach ($list as $key) {
            $this->downGear2($key);
        }
    }
}
