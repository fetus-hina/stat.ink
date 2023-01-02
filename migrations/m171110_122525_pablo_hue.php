<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171110_122525_pablo_hue extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'publo_hue',
            'Inkbrush Nouveau',
            'brush',
            'trap',
            'sphere',
            'pablo',
            null,
            1101,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('publo_hue');
    }
}
