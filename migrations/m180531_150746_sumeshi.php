<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180531_150746_sumeshi extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            [
                'splatnet' => 20,
                'release_at' => '2018-06-01T09:00:00+09:00',
            ],
            ['key' => 'sumeshi'],
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
            ['key' => 'sumeshi'],
        );
    }
}
