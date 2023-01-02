<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160101_024720_shottsuru_area extends Migration
{
    public function safeUp()
    {
        $this->update('map', [
            'area' => 3127,
            'release_at' => '2015-12-29T11:00:00+09:00',
        ], [
            'key' => 'shottsuru',
        ]);
    }

    public function safeDown()
    {
        $this->update('map', [
            'area' => null,
            'release_at' => null,
        ], [
            'key' => 'shottsuru',
        ]);
    }
}
