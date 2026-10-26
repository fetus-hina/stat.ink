<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

use function headers_sent;

final class LogoutAction extends BaseAction
{
    #[Override]
    public function run(): Response
    {
        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        Yii::$app->user->logout();
        return $this->controller->redirect(Yii::$app->homeUrl);
    }
}
