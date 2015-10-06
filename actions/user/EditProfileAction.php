<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\ProfileForm;

class EditProfileAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $ident = Yii::$app->user->getIdentity();
        $form = new ProfileForm();
        if ($request->isPost) {
            $form->load($request->bodyParams);
            if ($form->validate()) {
                $ident->attributes = $form->attributes;
                if ($ident->save()) {
                    $this->controller->redirect(['user/profile']);
                    return;
                }
            }
        } else {
            $form->attributes = $ident->attributes;
        }

        return $this->controller->render('edit-profile.tpl', [
            'user' => $ident,
            'form' => $form,
        ]);
    }
}
