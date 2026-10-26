<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use app\models\LoginForm;
use app\models\RegisterForm;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

class RegisterAction extends BaseAction
{
    public const METHOD_PASSKEY = 'passkey';
    public const METHOD_PASSWORD = 'password';

    #[Override]
    public function run(): string|Response
    {
        $request = Yii::$app->getRequest();

        // Passkey registration is the default; the form itself is handled by PasskeySignup*Action
        if (!$request->isPost && $request->get('method') !== self::METHOD_PASSWORD) {
            return $this->controller->render('register', [
                'method' => self::METHOD_PASSKEY,
                'register' => new RegisterForm(['scenario' => RegisterForm::SCENARIO_PASSKEY]),
            ]);
        }

        $form = new RegisterForm();
        if ($request->isPost) {
            $form->attributes = $request->post('RegisterForm');
            if ($form->validate()) {
                $user = $form->toUserModel();
                if ($user->save()) {
                    // ログインの動きを統一するためにログインフォームで認証かける
                    $login = new LoginForm();
                    $login->screen_name = $form->screen_name;
                    $login->password = $form->password;
                    if ($login->login()) {
                        return $this->controller->redirect(['user/profile']);
                    }
                }
            }
        }

        return $this->controller->render('register', [
            'method' => self::METHOD_PASSWORD,
            'register' => $form,
        ]);
    }
}
