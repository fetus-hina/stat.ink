<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/IkaLogLog/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;
use yii\web\NotFoundHttpException;
use app\models\RegisterForm;
use app\models\User;

class RegisterAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $form = new RegisterForm();
        if ($request->isPost) {
            $form->attributes = $request->post('RegisterForm');
            $form->recaptcha_token    = $request->post('recaptcha');
            $form->recaptcha_response = $request->post('g-recaptcha-response');
            if ($form->validate()) {
                $user = $form->toUserModel();
                if ($user->save()) {
                    echo "ok. saved.\n";
                    echo $user->id . "\n";
                    exit;
                }
            }
        }

        return $this->controller->render('login.tpl', [
            'login' => new RegisterForm(),
            'register' => $form,
        ]);
    }
}
