<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180316_100111_h3d extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'h3reelgun_d',
            'H-3 Nozzlenose D',
            'reelgun',
            'kyubanbomb',
            'armor',
            'h3reelgun',
            null,
            311,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('h3reelgun_d');
    }
}
