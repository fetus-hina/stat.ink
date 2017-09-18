<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use app\models\Battle2;
use app\models\User;
use app\models\UserStat2;
use yii\console\Controller;
use yii\helpers\Console;

class Battle2Controller extends Controller
{
    public function actionUserStat($id)
    {
        if (!$user = User::findOne(['id' => (int)$id])) {
            $this->stderr("Could not find user {$id}\n");
            return 1;
        }
        if (!$model = UserStat2::findOne(['user_id' => $user->id])) {
            $model = Yii::createObject([
                'class' => UserStat2::class,
                'user_id' => $user->id,
            ]);
        }
        if (!$model->makeUpdate()->save()) {
            $this->stderr("Could not create/update stats\n");
            return 1;
        }
        $this->stderr("updated.\n");
        return 0;
    }

    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $battle = Battle2::findOne(['id' => (int)(string)$id]);
        if (!$battle) {
            $this->stderr("Could not find specified battle \"{$id}\"\n", Console::FG_RED);
            return 1;
        }
        $battle->delete();
        $transaction->commit();
        $this->stderr("updated.\n");
        return 0;
    }
}
