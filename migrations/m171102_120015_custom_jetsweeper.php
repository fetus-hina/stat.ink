<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m171102_120015_custom_jetsweeper extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'jetsweeper_custom',
            'Custom Jet Squelcher',
            'shooter',
            'quickbomb',
            'presser',
            'jetsweeper',
            null,
            91,
        );
    }

    public function safeDown()
    {
        $this->downWeapon('jetsweeper_custom');
    }
}
