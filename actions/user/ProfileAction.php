<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use yii\web\ViewAction as BaseAction;

class ProfileAction extends BaseAction
{
    #[Override]
    public function run(): string
    {
        $ident = Yii::$app->user->getIdentity();
        return $this->controller->render('profile', [
            'user' => $ident,
        ]);
    }
}
