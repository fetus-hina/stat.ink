<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180202_092125_96gal_deco extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            '96gal_deco',
            '.96 Gal Deco',
            'shooter',
            'splashshield',
            'chakuchi',
            '96gal',
            null,
            81,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('96gal_deco');
    }
}
