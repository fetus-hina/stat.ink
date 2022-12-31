<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171221_093028_bottlegeyser extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'bottlegeyser',
            'Squeezer',
            'shooter',
            'splashshield',
            'presser',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('bottlegeyser');
    }
}
