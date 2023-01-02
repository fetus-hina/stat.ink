<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180309_093012_bold_neo extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'bold_neo',
            'Neo Sploosh-o-matic',
            'shooter',
            'jumpbeacon',
            'missile',
            'bold',
            null,
            1,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('bold_neo');
    }
}
