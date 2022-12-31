<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180424_175751_update_map extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            [
                'splatnet' => 16,
                'release_at' => '2018-04-25T11:00:00+09:00',
            ],
            ['key' => 'mongara'],
        );
        $this->update(
            'map2',
            [
                'area' => 3081,
                'release_at' => '2018-03-31T11:00:00+09:00',
            ],
            ['key' => 'shottsuru'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            ['splatnet' => null, 'release_at' => null],
            ['key' => 'mongara'],
        );
        $this->update(
            'map2',
            ['area' => null, 'release_at' => null],
            ['key' => 'shottsuru'],
        );
    }
}
