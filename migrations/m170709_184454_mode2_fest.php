<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170709_184454_mode2_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('mode2', [
            'key' => 'fest',
            'name' => 'Splatfest',
        ]);
        $this->insert('mode_rule2', [
            'mode_id' => (new Query())
                ->select('id')
                ->from('mode2')
                ->where(['key' => 'fest'])
                ->scalar(),
            'rule_id' => (new Query())
                ->select('id')
                ->from('rule2')
                ->where(['key' => 'nawabari'])
                ->scalar(),
        ]);
    }

    public function safeDown()
    {
        $modeId = (new Query())
            ->select('id')
            ->from('mode2')
            ->where(['key' => 'fest'])
            ->scalar();
        $this->delete('mode_rule2', ['mode_id' => $modeId]);
        $this->delete('mode2', ['id' => $modeId]);
    }
}
