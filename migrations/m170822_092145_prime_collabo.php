<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170822_092145_prime_collabo extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon('prime_collabo', 'Forge Splattershot Pro', 'shooter', 'kyubanbomb', 'bubble', 'prime');
    }

    public function safeDown()
    {
        $this->downWeapon('prime_collabo');
    }
}
