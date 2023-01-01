<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170714_185817_version2 extends Migration
{
    public function safeUp()
    {
        $this->insert('splatoon_version2', [
            'tag' => '0.1.0',
            'name' => 'Splatfest World Premiere',
            'released_at' => '2017-07-15 00:00:00+09',
        ]);
    }

    public function safeDown()
    {
        $this->delete('splatoon_version2', ['tag' => '0.1.0']);
    }
}
