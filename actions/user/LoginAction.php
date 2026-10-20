<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use app\models\LoginForm;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

class LoginAction extends BaseAction
{
    #[Override]
    public function run(): string|Response
    {
        $request = Yii::$app->getRequest();
        $form = new LoginForm();
        if ($request->isPost) {
            $form->attributes = $request->post('LoginForm');
            if ($form->login()) {
                return $this->controller->goBack(
                    ['show-user/profile', 'screen_name' => Yii::$app->user->identity->screen_name],
                );
            }
        }

        return $this->controller->render('login', [
            'login' => $form,
        ]);
    }
}
