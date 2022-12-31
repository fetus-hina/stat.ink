<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180316_071837_nike_gears extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            static::name2key('Sesame Salt 270s'),
            'Sesame Salt 270s',
            'shoes',
            'tentatek',
            'quick_super_jump',
            2037,
        );
        $this->upGear2(
            static::name2key('Sea Slug Volt 950s'),
            'Sea Slug Volt 950s',
            'shoes',
            'tentatek',
            'ink_saver_sub',
            3018,
        );
    }

    public function safeDown()
    {
        $this->downGear2(static::name2key('Sesame Salt 270s'));
        $this->downGear2(static::name2key('Sea Slug Volt 950s'));
    }
}
