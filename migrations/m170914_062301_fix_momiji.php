<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170914_062301_fix_momiji extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->update('weapon2', [
            'main_group_id' => $this->findWeaponId('wakaba'),
        ], [
            'key' => 'momiji',
        ]);
    }

    public function safeDown()
    {
        $this->update('weapon2', [
            'main_group_id' => $this->findWeaponId('momiji'),
        ], [
            'key' => 'momiji',
        ]);
    }
}
