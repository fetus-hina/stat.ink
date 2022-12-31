<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170915_102639_barrelspinner_deco extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'barrelspinner_deco',
            'Heavy Splatling Deco',
            'splatling',
            'splashshield',
            'bubble',
            'barrelspinner',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('barrelspinner_deco');
    }
}
