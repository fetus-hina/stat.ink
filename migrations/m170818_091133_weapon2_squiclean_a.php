<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170818_091133_weapon2_squiclean_a extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon('squiclean_a', 'Classic Squiffer', 'charger', 'pointsensor', 'armor');
    }

    public function safeDown()
    {
        $this->downWeapon('squiclean_a');
    }
}
