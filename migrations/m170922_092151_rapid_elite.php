<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170922_092151_rapid_elite extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'rapid_elite',
            'Rapid Blaster Pro',
            'blaster',
            'poisonmist',
            'amefurashi',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('rapid_elite');
    }
}
