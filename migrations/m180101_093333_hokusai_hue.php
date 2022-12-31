<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180101_093333_hokusai_hue extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'hokusai_hue',
            'Octobrush Nouveau',
            'brush',
            'jumpbeacon',
            'missile',
            'hokusai',
            null,
            1111,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('hokusai_hue');
    }
}
