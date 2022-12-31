<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171006_082501_bamboo14mk1 extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'bamboo14mk1',
            'Bamboozler 14 Mk I',
            'charger',
            'curlingbomb',
            'missile',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('bamboo14mk1');
    }
}
