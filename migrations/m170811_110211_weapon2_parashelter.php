<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170811_110211_weapon2_parashelter extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon('parashelter', 'Splat Brella', 'brella', 'sprinkler', 'amefurashi');
        $this->upWeapon(
            'heroshelter_replica',
            'Hero Brella Replica',
            'brella',
            'sprinkler',
            'amefurashi',
            'parashelter',
            'parashelter',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('heroshelter_replica');
        $this->downWeapon('parashelter');
    }
}
