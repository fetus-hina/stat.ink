<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170731_105212_version extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version2', ['tag', 'name', 'released_at'], [
            ['1.1.2', '1.1.2', '2017-07-27T10:00:00+09:00'],
        ]);
        $this->update(
            'battle2',
            ['version_id' => $this->getId('1.1.2')],
            ['>=', 'end_at', '2017-07-27T10:00:00+09:00'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'battle2',
            ['version_id' => $this->getId('1.0.0')],
            ['version_id' => $this->getId('1.1.2')],
        );
        $this->delete('splatoon_version2', ['tag' => '1.1.2']);
    }

    private function getid(string $tag): int
    {
        return (new Query())
            ->select('id')
            ->from('splatoon_version2')
            ->where(['tag' => $tag])
            ->limit(1)
            ->scalar();
    }
}
