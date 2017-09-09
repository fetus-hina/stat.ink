<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170908_094901_weapon2_momiji extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon('momiji', 'Custom Splattershot Jr.', 'shooter', 'robotbomb', 'amefurashi');
    }

    public function safeDown()
    {
        $this->downWeapon('momiji');
    }
}
