<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171013_094421_4k_custom extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'liter4k_custom',
            'Custom E-liter 4K',
            'charger',
            'jumpbeacon',
            'bubble',
            'liter4k',
            null,
            2031,
        );
        $this->upWeapon(
            'liter4k_scope_custom',
            'Custom E-liter 4K Scope',
            'charger',
            'jumpbeacon',
            'bubble',
            'liter4k',
            null,
            2041,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('liter4k_custom');
        $this->downWeapon('liter4k_scope_custom');
    }
}
