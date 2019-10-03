<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171006_081850_engawa_splatnet extends Migration
{
    public function safeUp()
    {
        $this->update('map2', [
            'splatnet' => 9,
            'release_at' => '2017-10-06T23:00:00+09:00',
        ], ['key' => 'engawa']);
    }

    public function safeDown()
    {
        $this->update('map2', [
            'splatnet' => null,
            'release_at' => null,
        ], ['key' => 'engawa']);
    }
}
