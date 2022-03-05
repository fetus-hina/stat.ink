<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\User;
use yii\db\Migration;

class m151021_110129_user_weapon_init extends Migration
{
    public function safeUp()
    {
        $select = (new \yii\db\Query())
            ->select([
                'user_id' => '{{battle}}.[[user_id]]',
                'weapon_id' => '{{battle}}.[[weapon_id]]',
                'count' => 'COUNT(*)',
            ])
            ->from('battle')
            ->andWhere(['not', ['{{battle}}.[[weapon_id]]' => null]])
            ->groupBy(implode(', ', ['{{battle}}.[[user_id]]', '{{battle}}.[[weapon_id]]']))
            ->createCommand()
            ->rawSql;
        $insert = 'INSERT INTO {{user_weapon}} ( [[user_id]], [[weapon_id]], [[count]] ) ' . $select;
        $this->execute($insert);
    }

    public function safeDown()
    {
        $this->delete('user_stat');
    }
}
