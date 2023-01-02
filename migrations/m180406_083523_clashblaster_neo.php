<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180406_083523_clashblaster_neo extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'clashblaster_neo',
            'Clash Blaster Neo',
            'blaster',
            'curlingbomb',
            'missile',
            'clashblaster',
            null,
            231,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('clashblaster_neo');
    }
}
