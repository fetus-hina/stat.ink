<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m180423_184707_rank_x extends Migration
{
    public function safeUp()
    {
        $this->insert('rank_group2', [
            'rank' => 50,
            'key' => 'x',
            'name' => 'X zone',
        ]);
        $groupId = (new Query())
            ->select('id')
            ->from('rank_group2')
            ->where(['key' => 'x'])
            ->scalar();
        $this->insert('rank2', [
            'group_id' => $groupId,
            'rank' => 50,
            'key' => 'x',
            'name' => 'X',
            'int_base' => 1010,
        ]);
    }

    public function safeDown()
    {
        $this->delete('rank2', ['key' => 'x']);
        $this->delete('rank_group2', ['key' => 'x']);
    }
}
