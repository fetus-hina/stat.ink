<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171106_171939_gear2 extends Migration
{
    public function safeUp()
    {
        $this->update(
            'gear2',
            ['key' => 'painters_mask', 'splatnet' => 8004],
            ['key' => 'painter_s_mask'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'gear2',
            ['key' => 'painter_s_mask', 'splatnet' => null],
            ['key' => 'painters_mask'],
        );
    }
}
