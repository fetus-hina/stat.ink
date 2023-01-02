<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180329_111017_sorella_brella extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'parashelter_sorella',
            'Sorella Brella',
            'brella',
            'robotbomb',
            'pitcher',
            'parashelter',
            null,
            6001,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('parashelter_sorella');
    }
}
