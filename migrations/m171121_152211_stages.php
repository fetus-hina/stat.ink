<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171121_152211_stages extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('map2', ['key', 'name', 'short_name', 'release_at'], [
            ['zatou', 'MakoMart', 'Mart', '2017-11-25T11:00:00+09:00'],
            ['arowana', 'Arowana Mall', 'Mall', null],
            ['hakofugu', 'Walleye Warehouse', 'Warehouse', null],
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => ['zatou', 'arowana', 'hakofugu']]);
    }
}
