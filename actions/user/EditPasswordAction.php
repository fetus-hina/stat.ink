<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Throwable;
use Yii;
use app\components\helpers\Password;
use app\models\PasswordForm;
use app\models\User;
use yii\web\ViewAction as BaseAction;

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
                        $this->sendEmail($ident);
                        $this->controller->redirect(['user/profile']);
                        return;
                    }
                } catch (Throwable $e) {
                }
                $transaction->rollback();
            }
        }

        return $this->controller->render('edit-password', [
            'user' => $ident,
            'form' => $form,
        ]);
    }

    private function sendEmail(User $user): void
    {
        if (!$user->email) {
            return;
        }

        Yii::$app->mailer
            ->compose(
                ['text' => '@app/views/email/change-password'],
                ['user' => $user],
            )
            ->setFrom(Yii::$app->params['notifyEmail'])
            ->setTo([$user->email => $user->name])
            ->setSubject(Yii::t(
                'app-email',
                '[{site}] {name} (@{screen_name}): Changed your password',
                [
                    'name' => $user->name,
                    'screen_name' => $user->screen_name,
                    'site' => Yii::$app->name,
                ],
                $user->emailLang->lang ?? 'en-US',
            ))
            ->send();
    }
}
