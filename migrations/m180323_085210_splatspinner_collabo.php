<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180323_085210_splatspinner_collabo extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'splatspinner_collabo',
            'Zink Mini Splatling',
            'splatling',
            'curlingbomb',
            'amefurashi',
            'splatspinner',
            null,
            4001,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('splatspinner_collabo');
    }
}
