<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170720_084422_splatoon2 extends Migration
{
    public function safeUp()
    {
        $this->insert('splatoon_version2', [
            'tag' => '1.0.0',
            'name' => '1.0.0',
            'released_at' => '2017-07-20 18:00:00+09',
        ]);
    }

    public function safeDown()
    {
        $this->delete('splatoon_version2', ['tag' => '1.0.0']);
    }
}
