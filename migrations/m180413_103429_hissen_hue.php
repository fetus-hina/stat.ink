<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180413_103429_hissen_hue extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'hissen_hue',
            'Tri-Slosher Nouveau',
            'slosher',
            'splashbomb',
            'amefurashi',
            'hissen',
            null,
            3011,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('hissen_hue');
    }
}
