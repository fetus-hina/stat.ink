<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180223_112003_nova_neo extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'nova_neo',
            'Luna Blaster Neo',
            'blaster',
            'trap',
            'pitcher',
            'nova',
            null,
            201,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('nova_neo');
    }
}
