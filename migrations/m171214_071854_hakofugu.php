<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171214_071854_hakofugu extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['splatnet' => 14], ['key' => 'hakofugu']);
    }

    public function safeDown()
    {
        $this->update('map2', ['splatnet' => null], ['key' => 'hakofugu']);
    }
}
