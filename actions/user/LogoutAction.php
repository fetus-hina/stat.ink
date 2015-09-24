<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;

class LogoutAction extends BaseAction
{
    public function run()
    {
        Yii::$app->user->logout();
        return $this->controller->redirect(Yii::$app->homeUrl);
    }
}
