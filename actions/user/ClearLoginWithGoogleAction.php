<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Yii;
use yii\helpers\Url;
use yii\web\ViewAction as BaseAction;

class ClearLoginWithGoogleAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->response;

        $user = Yii::$app->user->identity;
        $info = $user->loginWithGoogle;
        if ($info) {
            $info->delete();
        }
        return $response->redirect(Url::to(['user/profile'], true), 303);
    }
}
