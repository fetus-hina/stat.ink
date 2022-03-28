<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170904_111014_version2_1_3_0 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version2', ['tag', 'name', 'released_at'], [
            ['1.3.0', '1.3.0', '2017-09-08T10:00:00+09:00'],
        ]);
    }

    public function safeDown()
    {
        $this->update(
            'battle2',
            ['version_id' => $this->getId('1.3.0')],
            ['version_id' => $this->getId('1.2.0')]
        );
        $this->delete('splatoon_version2', ['tag' => '1.3.0']);
    }

    private function getId(string $tag): int
    {
        return (new Query())
            ->select('id')
            ->from('splatoon_version2')
            ->where(['tag' => $tag])
            ->limit(1)
            ->scalar();
    }
}
