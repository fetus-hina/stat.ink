<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180126_090404_rapid_deco extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'rapid_deco',
            'Rapid Blaster Deco',
            'blaster',
            'kyubanbomb',
            'jetpack',
            'rapid',
            null,
            241,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('rapid_deco');
    }
}
