<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\models\User;
use yii\helpers\Url;
use yii\web\ViewAction as BaseAction;

class RegenerateApikeyAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->response;
        $user = Yii::$app->user->identity;
        $user->api_key = User::generateNewApiKey();
        $user->save();
        return $response->redirect(Url::to(['user/profile'], true), 303);
    }
}
