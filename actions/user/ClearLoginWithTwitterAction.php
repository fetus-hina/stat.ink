<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use yii\helpers\Url;
use yii\web\ViewAction as BaseAction;

class ClearLoginWithTwitterAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->response;

        $user = Yii::$app->user->identity;
        $info = $user->loginWithTwitter;
        if ($info) {
            $info->delete();
        }
        return $response->redirect(Url::to(['user/profile'], true), 303);
    }
}
