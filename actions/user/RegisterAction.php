<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\User;
use yii\web\ViewAction as BaseAction;

class RegisterAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
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
            'register' => $form,
        ]);
    }
}
