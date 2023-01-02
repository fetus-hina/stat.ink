<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180420_090724_longblaster_custom extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'longblaster_custom',
            'Custom Range Blaster',
            'blaster',
            'curlingbomb',
            'bubble',
            'longblaster',
            null,
            221,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('longblaster_custom');
    }
}
