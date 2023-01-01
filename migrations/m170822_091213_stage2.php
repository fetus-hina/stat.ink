<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170822_091213_stage2 extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'manta',
            'name' => 'Manta Maria',
            'short_name' => 'Manta',
            'release_at' => '2017-08-26T00:00:00+09',
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => 'manta']);
    }
}
