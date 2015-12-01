<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\components\helpers\Password;
use app\models\PasswordForm;

class EditPasswordAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $ident = Yii::$app->user->getIdentity();
        $form = new PasswordForm();
        $form->screen_name = $ident->screen_name;
        if ($request->isPost) {
            $form->load($request->bodyParams);
            $form->screen_name = $ident->screen_name;
            if ($form->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $ident->password = Password::hash($form->new_password);
                    if ($ident->save()) {
                        $transaction->commit();
                        $this->controller->redirect(['user/profile']);
                        return;
                    }
                } catch (\Exception $e) {
                }
                $transaction->rollback();
            }
        }

        return $this->controller->render('edit-password.tpl', [
            'user' => $ident,
            'form' => $form,
        ]);
    }
}
