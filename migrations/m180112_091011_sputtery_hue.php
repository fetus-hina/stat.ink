<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180112_091011_sputtery_hue extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'sputtery_hue',
            'Dapple Dualies Nouveau',
            'maneuver',
            'poisonmist',
            'amefurashi',
            'sputtery',
            null,
            5001,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('sputtery_hue');
    }
}
