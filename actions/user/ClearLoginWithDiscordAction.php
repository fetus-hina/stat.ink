<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

class ClearLoginWithDiscordAction extends BaseAction
{
    #[Override]
    public function run(): Response
    {
        $response = Yii::$app->response;

        $user = Yii::$app->user->identity;
        $info = $user->loginWithDiscord;
        if ($info) {
            $info->delete();
        }
        return $response->redirect(Url::to(['user/profile'], true), 303);
    }
}
