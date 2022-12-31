<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180208_090239_soytuber_custom extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'soytuber_custom',
            'Custom Goo Tuber',
            'charger',
            'curlingbomb',
            'jetpack',
            'soytuber',
            null,
            2061,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('soytuber_custom');
    }
}
