<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180216_153944_screwslosher_neo extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'screwslosher_neo',
            'Sloshing Machine Neo',
            'slosher',
            'pointsensor',
            'pitcher',
            'screwslosher',
            null,
            3021,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('screwslosher_neo');
    }
}
