<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show;

use Yii;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class UserStatWeaponAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        return $this->controller->redirect(
            ['show/user-stat-by-weapon', 'screen_name' => $user->screen_name],
            301,
        );
    }
}
