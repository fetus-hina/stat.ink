<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180301_103246_quadhopper_black extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'quadhopper_black',
            'Dark Tetra Dualies',
            'maneuver',
            'robotbomb',
            'chakuchi',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('quadhopper_black');
    }
}
