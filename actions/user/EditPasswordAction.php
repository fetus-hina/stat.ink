<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use RuntimeException;
use Throwable;
use Yii;
use app\components\helpers\PasskeyReauth;
use app\models\PasswordForm;
use app\models\User;
use yii\web\ViewAction as BaseAction;

class EditPasswordAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $ident = Yii::$app->user->getIdentity();
        $hasPassword = $ident->hasPassword();
        $form = new PasswordForm();
        if (!$hasPassword) {
            $form->scenario = PasswordForm::SCENARIO_SET;
        }
        $form->screen_name = $ident->screen_name;
        if ($request->isPost) {
            $form->load($request->bodyParams);
            $form->screen_name = $ident->screen_name;
            if ($form->validate() && !$hasPassword && !PasskeyReauth::consume((int)$ident->id)) {
                $form->addError(
                    'new_password',
                    Yii::t('app-passkey', 'Failed to verify with your passkey.'),
                );
            }
            if (!$form->hasErrors()) {
                if ($this->changePassword($ident, $form->new_password)) {
                    // Sent after the commit; a mail failure must not look like a failed change
                    $this->sendEmail($ident);
                    return $this->controller->redirect([
                        'user/profile',
                        'recovery_keys_revoked' => 1,
                    ]);
                }

                $form->addError(
                    'new_password',
                    Yii::t('app-user', 'Could not change your password.'),
                );
            }
        }

        return $this->controller->render('edit-password', [
            'user' => $ident,
            'form' => $form,
            'hasPassword' => $hasPassword,
        ]);
    }

    private function changePassword(User $user, string $newPassword): bool
    {
        try {
            Yii::$app->db->transaction(function () use ($user, $newPassword): void {
                if (!$user->changePassword($newPassword)) {
                    throw new RuntimeException('Failed to change password');
                }
            });
            return true;
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            $user->refresh();
            return false;
        }
    }

    private function sendEmail(User $user): void
    {
        if (!$user->email) {
            return;
        }

        try {
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
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
        }
    }
}
