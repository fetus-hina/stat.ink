<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171027_090334_dynamo_tesla extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'dynamo_tesla',
            'Gold Dynamo Roller',
            'roller',
            'splashbomb',
            'armor',
            'dynamo',
            null,
            1021,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('dynamo_tesla');
    }
}
