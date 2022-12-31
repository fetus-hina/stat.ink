<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171214_134752_bucketslosher_deco extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'bucketslosher_deco',
            'Slosher Deco',
            'slosher',
            'sprinkler',
            'sphere',
            'bucketslosher',
            null,
            3001,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('bucketslosher_deco');
    }
}
