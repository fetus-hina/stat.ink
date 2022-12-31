<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171201_085415_kelvin525 extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'kelvin525',
            'Glooga Dualies',
            'maneuver',
            'trap',
            'jetpack',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('kelvin525');
    }
}
