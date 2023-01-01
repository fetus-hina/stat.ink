<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170710_121531_gachi2 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('lobby2', ['key', 'name'], [
            ['squad_2', 'Squad Battle (Twin)'],
            ['squad_3', 'Squad Battle (Tri)'],
            ['squad_4', 'Squad Battle (Quad)'],
        ]);

        $this->insert('mode2', [
            'key' => 'gachi',
            'name' => 'Ranked Battle',
        ]);

        $this->batchInsert('rule2', ['key', 'name', 'short_name'], [
            ['area', 'Splat Zones', 'SZ'],
            ['yagura', 'Tower Control', 'TC'],
            ['hoko', 'Rainmaker', 'RM'],
        ]);

        $modeId = (new Query())
            ->select('id')
            ->from('mode2')
            ->where(['key' => 'gachi'])
            ->scalar();
        $this->batchInsert('mode_rule2', ['mode_id', 'rule_id'], array_map(
            fn (int $ruleId): array => [(int)$modeId, $ruleId],
            (new Query())
                ->select('id')
                ->from('rule2')
                ->where(['key' => ['area', 'yagura', 'hoko']])
                ->column(),
        ));
    }

    public function safeDown()
    {
        $rules = (new Query())
            ->select('id')
            ->from('rule2')
            ->where(['key' => ['area', 'yagura', 'hoko']])
            ->column();
        $this->delete('mode_rule2', ['rule_id' => $rules]);
        $this->delete('rule2', ['id' => $rules]);
        $this->delete('mode2', ['key' => 'gachi']);
    }
}
