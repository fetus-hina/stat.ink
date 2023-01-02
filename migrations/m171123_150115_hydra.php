<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171123_150115_hydra extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'hydra',
            'Hydra Splatling',
            'splatling',
            'robotbomb',
            'chakuchi',
            null,
            null,
            4020,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('hydra');
    }
}
