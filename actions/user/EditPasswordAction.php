<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use RuntimeException;
use Throwable;
use Yii;
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
                try {
                    Yii::$app->db->transaction(function () use ($ident, $form): void {
                        if (!$ident->changePassword($form->new_password)) {
                            throw new RuntimeException('Failed to change password');
                        }
                    });
                    $this->sendEmail($ident);
                    $this->controller->redirect([
                        'user/profile',
                        'recovery_keys_revoked' => 1,
                    ]);
                    return;
                } catch (Throwable $e) {
                }
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
