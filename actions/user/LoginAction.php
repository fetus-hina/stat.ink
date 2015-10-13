<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\LoginForm;
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
                return $this->controller->goBack(
                    ['show/user', 'screen_name' => Yii::$app->user->identity->screen_name ]
                );
            }
        }

        return $this->controller->render('login.tpl', [
            'login' => $form,
        ]);
    }
}
