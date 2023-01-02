<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180330_181021_pit_splatnet extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            [
                'splatnet' => 17,
                'release_at' => '2018-03-31T11:00:00+09',
            ],
            ['key' => 'shottsuru'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            [
                'splatnet' => null,
                'release_at' => null,
            ],
            ['key' => 'shottsuru'],
        );
    }
}
