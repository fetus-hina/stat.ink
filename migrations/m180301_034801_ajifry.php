<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180301_034801_ajifry extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'ajifry',
            'name' => 'Goby Arena',
            'short_name' => 'Arena',
            'release_at' => '2018-03-02T11:00:00+09:00',
            'splatnet' => 16,
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => 'ajifry']);
    }
}
