<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170804_103326_weapon2_bold extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon('bold', 'Sploosh-o-matic', 'shooter', 'curlingbomb', 'chakuchi');
    }

    public function safeDown()
    {
        $this->downWeapon('bold');
    }
}
