<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160703_121921_version2_9 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version', ['tag', 'name', 'released_at'], [
            [ '2.9.0', '2.9.0', '2016-07-06T14:00:00+09:00' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('splatoon_version', ['tag' => '2.9.0']);
    }
}
