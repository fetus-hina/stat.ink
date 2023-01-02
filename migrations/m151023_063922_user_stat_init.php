<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\User;
use app\models\UserStat;
use yii\db\Migration;

class m151023_063922_user_stat_init extends Migration
{
    public function safeUp()
    {
        foreach (User::find()->orderBy('id')->all() as $user) {
            $stat = $user->userStat ?: new UserStat();
            $stat->user_id = $user->id;
            $stat->createCurrentData();
            if (!$stat->save()) {
                return false;
            }
        }
    }

    public function safeDown()
    {
        $this->delete('user_stat');
    }
}
