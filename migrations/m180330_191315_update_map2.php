<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180330_191315_update_map2 extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            [
                'area' => 2052,
                'splatnet' => 12,
                'release_at' => '2018-01-12T11:00:00+09:00',
            ],
            ['key' => 'devon'],
        );
        $this->update(
            'map2',
            [
                'area' => 2391,
                'splatnet' => 15,
                'release_at' => '2018-02-02T11:00:00+09:00',
            ],
            ['key' => 'arowana'],
        );
        $this->update('map2', ['area' => 1632], ['key' => 'hakofugu']);
        $this->update('map2', ['area' => 2221], ['key' => 'ajifry']);
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            [
                'area' => null,
                'splatnet' => null,
            ],
            [
                'key' => [
                    'devon',
                    'arowana',
                ],
            ],
        );
        $this->update('map2', ['area' => null], ['key' => ['hakofugu', 'ajifry']]);
    }
}
