<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180614_211329_octoshot extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'octoshooter_replica',
            'Octo Shot Replica',
            'shooter',
            'splashbomb',
            'jetpack',
            'sshooter',
            'sshooter_collabo',
            46,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('octoshooter_replica');
    }
}
