<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170806_090715_fix_hero_dualie_replicas extends Migration
{
    public function safeUp()
    {
        $this->update(
            'weapon2',
            ['name' => 'Hero Dualie Replicas'],
            ['key' => 'heromaneuver_replica'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'weapon2',
            ['name' => 'Hero Dualies Replica'],
            ['key' => 'heromaneuver_replica'],
        );
    }
}
