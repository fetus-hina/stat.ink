<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160710_195736_fix_hokke_release extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map',
            ['release_at' => '2015-06-02 11:00:00+09'],
            ['key' => 'hokke'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'map',
            ['release_at' => '2015-05-28 00:00:00+09'],
            ['key' => 'hokke'],
        );
    }
}
