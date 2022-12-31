<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\helpers\Battle as BattleHelper;
use yii\db\Query;

class m171108_185946_v1_4_2 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version2', [ 'tag', 'name', 'released_at' ], [
            ['1.4.2', '1.4.2', '2017-11-01T10:00:00+09:00'],
        ]);
        $this->update(
            'battle2',
            ['version_id' => $this->getId('1.4.2')],
            ['>=',
                'period',
                BattleHelper::calcPeriod2(
                    (new DateTimeImmutable('2017-11-01T11:00:00+09:00'))
                        ->getTimestamp()
                ),
            ]
        );
    }

    public function safeDown()
    {
        $this->update(
            'battle2',
            ['version_id' => $this->getId('1.4.1')],
            ['version_id' => $this->getId('1.4.2')]
        );
        $this->delete('splatoon_version2', ['tag' => '1.4.2']);
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
