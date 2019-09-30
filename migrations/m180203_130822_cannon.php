<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m180203_130822_cannon extends Migration
{
    public function safeUp()
    {
        $type = (new Query())
            ->select('id')
            ->from('death_reason_type2')
            ->where(['key' => 'gadget'])
            ->limit(1)
            ->scalar();

        $this->insert('death_reason2', [
            'key' => 'cannon',
            'name' => 'Cannon',
            'type_id' => $type,
            'weapon_id' => null,
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason2', ['key' => 'cannon']);
    }
}
