<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/IkaLogLog/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\User;

class LoginAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $form = new LoginForm();
        if ($request->isPost) {
            $form->attributes = $request->post('LoginForm');
            if ($form->login()) {
                return $this->controller->redirect(Yii::$app->user->getReturnUrl());
            }
        }

        return $this->controller->render('login.tpl', [
            'login' => $form,
            'register' => new RegisterForm(),
        ]);
    }
}
