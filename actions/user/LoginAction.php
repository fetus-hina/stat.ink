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
use yii\web\ServerErrorHttpException;
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
                if (!$user = Yii::$app->user->identity) {
                    throw new ServerErrorHttpException();
                }

                return $this->controller->goBack(
                    ['show-user/profile', 'screen_name' => $user->screen_name],
                );
            }
        }

        return $this->controller->render('login', [
            'login' => $form,
        ]);
    }
}
