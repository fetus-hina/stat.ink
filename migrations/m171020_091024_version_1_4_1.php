<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\helpers\Battle as BattleHelper;
use yii\db\Query;

class m171020_091024_version_1_4_1 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version2', ['tag', 'name', 'released_at'], [
            ['1.4.1', '1.4.1', '2017-10-20T10:00:00+09:00'],
        ]);
        $this->update(
            'battle2',
            ['version_id' => $this->getId('1.4.1')],
            ['>=',
                'period',
                BattleHelper::calcPeriod2(
                    (new DateTimeImmutable('2017-10-20T11:00:00+09:00'))
                        ->getTimestamp()
                ),
            ]
        );
    }

    public function safeDown()
    {
        $this->update(
            'battle2',
            ['version_id' => $this->getId('1.4.0')],
            ['version_id' => $this->getId('1.4.1')]
        );
        $this->delete('splatoon_version2', ['tag' => '1.4.1']);
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
