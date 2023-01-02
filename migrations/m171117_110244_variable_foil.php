<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171117_110244_variable_foil extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'variableroller_foil',
            'Foil Flingza Roller',
            'roller',
            'kyubanbomb',
            'missile',
            'variableroller',
            null,
            1031,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('variableroller_foil');
    }
}
