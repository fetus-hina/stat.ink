<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180105_095249_l3d extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'l3reelgun_d',
            'L-3 Nozzlenose D',
            'reelgun',
            'quickbomb',
            'jetpack',
            'l3reelgun',
            null,
            301,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('l3reelgun_d');
    }
}
