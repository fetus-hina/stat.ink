<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160609_092951_ikalog_version_2016060801 extends Migration
{
    public function safeUp()
    {
        $this->insert('ikalog_requirement', [
            'from' => '2016-06-09 00:55:08+09:00',
            'version_date' => '2016-06-08_01',
        ]);
    }

    public function safeDown()
    {
        $this->delete('ikalog_requirement', ['from' => '2016-06-09 00:55:08+09:00']);
    }
}
