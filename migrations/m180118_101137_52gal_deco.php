<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180118_101137_52gal_deco extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            '52gal_deco',
            '.52 Gal Deco',
            'shooter',
            'curlingbomb',
            'presser',
            '52gal',
            null,
            51,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('52gal_deco');
    }
}
