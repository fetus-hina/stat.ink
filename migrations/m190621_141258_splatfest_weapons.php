<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m190621_141258_splatfest_weapons extends Migration
{
    public function safeUp()
    {
        $type = (new Query())
            ->select('id')
            ->from('death_reason_type2')
            ->where(['key' => 'gadget'])
            ->limit(1)
            ->scalar();

        $this->batchInsert('death_reason2', ['key', 'name', 'type_id', 'weapon_id'], [
            ['senpaicannon', 'Senpai Cannon', $type, null],
            ['iidabomb', 'Marina Bomb', $type, null],
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason2', ['key' => ['senpaicannon', 'iidabomb']]);
    }
}
