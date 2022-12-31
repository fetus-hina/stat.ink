<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Rule;
use app\models\TurfwarWinBonus;
use yii\db\Migration;

class m160819_094109_nawabari_bonus_update extends Migration
{
    public function safeUp()
    {
        $nawabari = Rule::findOne(['key' => 'nawabari'])->id;

        $this->execute('LOCK TABLE {{battle}} IN EXCLUSIVE MODE');
        $this->execute(
            'ALTER TABLE {{battle}} ADD COLUMN [[bonus_id]] INTEGER NULL REFERENCES {{turfwar_win_bonus}}([[id]])',
        );
        $this->update(
            'battle',
            ['bonus_id' => TurfwarWinBonus::find()->at('2015-05-28T00:00:01+09:00')->one()->id],
            ['and',
                ['rule_id' => $nawabari],
                ['<', 'at', '2016-07-24T19:03:00+09:00'],
            ],
        );
        $this->update(
            'battle',
            ['bonus_id' => TurfwarWinBonus::find()->at('2016-07-24T19:03:01+09:00')->one()->id],
            ['and',
                ['rule_id' => $nawabari],
                ['>=', 'at', '2016-07-24T19:03:00+09:00'],
            ],
        );
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[bonus_id]]');
    }
}
