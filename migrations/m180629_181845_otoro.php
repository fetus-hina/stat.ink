<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180629_181845_otoro extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'otoro',
            'name' => 'New Albacore Hotel',
            'short_name' => 'Hotel',
            'splatnet' => 19,
            'release_at' => '2018-07-01T09:00:00+09:00',
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => 'otoro']);
    }
}
