<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170731_110807_private_match extends Migration
{
    public function safeUp()
    {
        $this->insert('lobby2', [
            'key' => 'private',
            'name' => 'Private Battle',
        ]);
        $this->insert('mode2', [
            'key' => 'private',
            'name' => 'Private Battle',
        ]);
        $modeId = (new Query())
            ->select('id')
            ->from('mode2')
            ->where(['key' => 'private'])
            ->limit(1)
            ->scalar();
        $this->batchInsert('mode_rule2', ['mode_id', 'rule_id'], array_map(
            fn ($rule) => [$modeId, $rule['id']],
            (new Query())
                ->select('*')
                ->from('rule2')
                ->where(['key' => ['nawabari', 'area', 'yagura', 'hoko']])
                ->all(),
        ));
    }

    public function safeDown()
    {
        $modeId = (new Query())
            ->select('id')
            ->from('mode2')
            ->where(['key' => 'private'])
            ->limit(1)
            ->scalar();
        $this->delete('mode_rule2', ['mode_id' => $modeId]);
        $this->delete('mode2', ['id' => $modeId]);
        $this->delete('lobby2', ['key' => 'private']);
    }
}
