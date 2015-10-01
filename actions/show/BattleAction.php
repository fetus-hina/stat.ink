<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\Battle;
use app\models\User;
use app\components\helpers\DateTimeFormatter;

class BattleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException('指定されたユーザが見つかりません');
        }

        $battle = Battle::findOne([
            'user_id' => $user->id,
            'id' => $request->get('battle'),
        ]);
        if (!$battle) {
            throw new NotFoundHttpException('指定されたバトルが見つかりません');
        }

        return $this->controller->render('battle.tpl', [
            'user' => $user,
            'battle' => $battle,
        ]);
    }
}
