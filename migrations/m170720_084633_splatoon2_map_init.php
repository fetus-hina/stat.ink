<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170720_084633_splatoon2_map_init extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            ['release_at' => '2017-07-15 17:00:00+09'],
            ['key' => ['combu', 'gangaze', 'ama', 'tachiuo']]
        );
        $this->update(
            'map2',
            ['release_at' => '2017-07-21 00:00:00+09'],
            ['key' => ['chozame', 'hokke']]
        );
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            ['release_at' => null],
            [
                'key' => [
                    'combu', 'gangaze', 'ama', 'tachiuo', 'chozame', 'hokke',
                ],
            ]
        );
    }
}
