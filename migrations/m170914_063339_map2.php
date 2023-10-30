<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170914_063339_map2 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('map2', ['key', 'name', 'short_name', 'release_at'], [
            ['mozuku', 'Kelp Dome', 'Dome', '2017-09-16T11:00:00+09:00'],
            ['engawa', 'Snapper Canal', 'Canal', null],
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', [
            'key' => [
                'mozuku',
                'engawa',
            ],
        ]);
    }
}
