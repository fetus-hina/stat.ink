<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use app\models\SplatoonVersion;

class m160708_074713_fix_version_2_10 extends Migration
{
    public function safeUp()
    {
        $this->update(
            'splatoon_version',
            ['released_at' => '2016-07-08T14:00:00+09:00'],
            ['tag' => '2.10.0'],
        );
        $this->update(
            'battle',
            ['version_id' => SplatoonVersion::findOne(['tag' => '2.10.0'])->id],
            ['>=', 'end_at', '2016-07-08T14:00:00+09:00'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'splatoon_version',
            ['released_at' => '2016-07-06T14:00:00+09:00'],
            ['tag' => '2.10.0'],
        );
    }
}
