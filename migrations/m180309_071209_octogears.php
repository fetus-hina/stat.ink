<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180309_071209_octogears extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            static::name2key('Studio Octophones'),
            'Studio Octophones',
            'headgear',
            'cuttlegear',
            'ink_recovery_up',
            5005,
        );
        $this->upGear2(
            static::name2key('Octo Layered LS'),
            'Octo Layered LS',
            'clothing',
            'cuttlegear',
            'ink_saver_main',
            3012,
        );
    }

    public function safeDown()
    {
        $this->downGear2(static::name2key('Studio Octophones'));
        $this->downGear2(static::name2key('Octo Layered LS'));
    }
}
