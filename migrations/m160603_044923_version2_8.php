<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160603_044923_version2_8 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version', ['tag', 'name', 'released_at'], [
            [ '2.8.0', '2.8.0', '2016-06-08T10:00:00+09:00' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('splatoon_version', ['tag' => '2.8.0']);
    }
}
