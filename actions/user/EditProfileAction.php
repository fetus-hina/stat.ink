<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\user;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Environment;
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
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $ident->attributes = $form->attributes;
                    $ident->env_id = $this->findOrCreateEnvironmentId($form->env);
                    if ($ident->save()) {
                        $transaction->commit();
                        $this->controller->redirect(['user/profile']);
                        return;
                    }
                } catch (\Exception $e) {
                }
                $transaction->rollback();
            }
        } else {
            $form->attributes = $ident->attributes;
            if ($ident->env) {
                $form->env = $ident->env->text;
            }
        }

        return $this->controller->render('edit-profile.tpl', [
            'user' => $ident,
            'form' => $form,
        ]);
    }

    protected function findOrCreateEnvironmentId($text)
    {
        $text = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", (string)$text);
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        $hash = rtrim(base64_encode(hash('sha256', $text, true)), '=');
        $model = Environment::findOne(['sha256sum' => $hash]);
        if ($model) {
            return $model->id;
        }

        $model = new Environment();
        $model->sha256sum = $hash;
        $model->text = $text;
        if (!$model->save()) {
            throw new \Exception();
        }
        return $model->id;
    }
}
