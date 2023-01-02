<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170720_095637_brella extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon_category2', [
            'key' => 'brella',
            'name' => 'Brellas',
        ]);
        $this->insert('weapon_type2', [
            'key' => 'brella',
            'name' => 'Brellas',
            'category_id' => (new Query())
                ->select('id')
                ->from('weapon_category2')
                ->where(['key' => 'brella'])
                ->scalar(),
        ]);
    }

    public function safeDown()
    {
        $this->delete('weapon_type2', ['key' => 'brella']);
        $this->delete('weapon_category2', ['key' => 'brella']);
    }
}
