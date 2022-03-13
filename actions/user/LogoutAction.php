<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\T;
use yii\base\Action;

final class LogoutAction extends Action
{
    public function run()
    {
        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        Yii::$app->user->logout();
        return T::webController($this->controller)
            ->redirect(Yii::$app->homeUrl);
    }
}
