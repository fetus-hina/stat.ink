<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\SplatoonVersion;
use yii\db\Migration;

class m160804_131656_version_2_11 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version', ['tag', 'name', 'released_at'], [
            [ '2.11.0', '2.11.0', '2016-08-03T10:00:00+09:00' ],
        ]);
        $this->update(
            'battle',
            ['version_id' => SplatoonVersion::findOne(['tag' => '2.11.0'])->id],
            ['>=', 'end_at', '2016-08-03T10:00:00+09:00']
        );
    }

    public function safeDown()
    {
        $this->update(
            'battle',
            ['version_id' => SplatoonVersion::findOne(['tag' => '2.10.0'])->id],
            ['version_id' => SplatoonVersion::findOne(['tag' => '2.11.0'])->id]
        );
        $this->delete('splatoon_version', ['tag' => '2.11.0']);
    }
}
