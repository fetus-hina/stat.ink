<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\T;
use app\models\LoginForm;
use yii\base\Action;

final class LoginAction extends Action
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $form = new LoginForm();
        if ($request->isPost) {
            $form->attributes = $request->post('LoginForm');
            if ($form->login()) {
                return T::webController($this->controller)
                    ->goBack(['show-user/profile',
                        'screen_name' => Yii::$app->user->identity->screen_name,
                    ]);
            }
        }

        return $this->controller->render('login', [
            'login' => $form,
        ]);
    }
}
