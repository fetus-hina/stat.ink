<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170801_112751_mystery_zone extends Migration
{
    public function safeUp()
    {
        $this->insert('{{map2}}', [
            'key' => 'mystery',
            'name' => 'Shifty Station',
            'short_name' => 'Shifty',
            'release_at' => '2017-08-04T15:00:00+09',
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{map2}}', ['key' => 'mystery']);
    }
}
