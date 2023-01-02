<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171208_100736_nzap89 extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'nzap89',
            'N-ZAP \'89',
            'shooter',
            'robotbomb',
            'missile',
            'nzap85',
            null,
            61,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('nzap89');
    }
}
