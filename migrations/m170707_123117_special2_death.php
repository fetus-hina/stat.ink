<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170707_123117_special2_death extends Migration
{
    public function safeUp()
    {
        $typeId = (int)(new Query())
            ->select('id')
            ->from('death_reason_type2')
            ->where(['key' => 'special'])
            ->scalar();
        $specialId = (int)(new Query())
            ->select('id')
            ->from('special2')
            ->where(['key' => 'sphere'])
            ->scalar();
        $this->insert('death_reason2', [
            'key' => 'sphere',
            'name' => 'Baller',
            'type_id' => $typeId,
            'special_id' => $specialId,
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason2', ['key' => 'sphere']);
    }
}
