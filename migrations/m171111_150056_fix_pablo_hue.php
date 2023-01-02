<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171111_150056_fix_pablo_hue extends Migration
{
    public function safeUp()
    {
        $this->update(
            'weapon2',
            ['key' => 'pablo_hue'],
            ['key' => 'publo_hue'],
        );
        $this->update(
            'death_reason2',
            ['key' => 'pablo_hue'],
            ['key' => 'publo_hue'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'weapon2',
            ['key' => 'publo_hue'],
            ['key' => 'pablo_hue'],
        );
        $this->update(
            'death_reason2',
            ['key' => 'publo_hue'],
            ['key' => 'pablo_hue'],
        );
    }
}
