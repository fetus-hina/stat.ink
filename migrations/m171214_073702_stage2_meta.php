<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171214_073702_stage2_meta extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['release_at' => '2017-12-15T11:00:00+09:00'], ['key' => 'hakofugu']);
        $this->update('map2', ['area' => 2166], ['key' => 'zatou']);
    }

    public function safeDown()
    {
        $this->update('map2', ['area' => null], ['key' => 'zatou']);
        $this->update('map2', ['release_at' => null], ['key' => 'hakofugu']);
    }
}
