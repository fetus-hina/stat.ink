<?php
use yii\db\Migration;
use app\models\User;
use app\models\UserStat;

class m151009_120811_user_stat_init extends Migration
{
    public function safeUp()
    {
        foreach (User::find()->orderBy('id')->all() as $user)
        {
            $stat = new UserStat();
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
