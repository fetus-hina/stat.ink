<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170807_102118_fix_hero_brush extends Migration
{
    public function safeUp()
    {
        $this->update(
            'weapon2',
            ['name' => 'Herobrush Replica'],
            ['key' => 'herobrush_replica'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'weapon2',
            ['name' => 'Hero Brush Replica'],
            ['key' => 'herobrush_replica'],
        );
    }
}
