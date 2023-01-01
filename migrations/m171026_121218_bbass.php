<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171026_121218_bbass extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'bbass',
            'name' => 'Blackbelly Skatepark',
            'short_name' => 'Skatepark',
            'splatnet' => null,
            'release_at' => '2017-10-28T11:00:00+09:00',
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => 'bbass']);
    }
}
