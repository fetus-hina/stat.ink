<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180316_081159_fix extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->update(
            'gear2',
            [
                'key' => static::name2key('Sea Slug Volt 95s'),
                'name' => 'Sea Slug Volt 95s',
            ],
            ['key' => static::name2key('Sea Slug Volt 950s')],
        );
    }

    public function safeDown()
    {
        $this->update(
            'gear2',
            [
                'key' => static::name2key('Sea Slug Volt 950s'),
                'name' => 'Sea Slug Volt 950s',
            ],
            ['key' => static::name2key('Sea Slug Volt 95s')],
        );
    }
}
