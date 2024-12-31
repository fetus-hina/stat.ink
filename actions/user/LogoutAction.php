<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;

use function headers_sent;

final class LogoutAction extends BaseAction
{
    public function run()
    {
        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        Yii::$app->user->logout();
        return $this->controller->redirect(Yii::$app->homeUrl);
    }
}
