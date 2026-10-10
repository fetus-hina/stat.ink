<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use RuntimeException;
use Throwable;
use Yii;
use app\components\helpers\PasskeyReauth;
use app\models\User;
use yii\web\ViewAction as BaseAction;

/**
 * Disables (forgets) the password of a user who has at least one passkey
 */
final class DisablePasswordAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident instanceof User) {
            return $this->controller->redirect(['user/login']);
        }

        $passkeyCount = (int)$ident->getUserPasskeys()->count();
        if (!$ident->hasPassword() || $passkeyCount < 1) {
            return $this->controller->redirect(['user/profile']);
        }

        $errorMessage = null;
        if (Yii::$app->request->isPost) {
            if (!PasskeyReauth::consume((int)$ident->id)) {
                $errorMessage = Yii::t('app-passkey', 'Failed to verify with your passkey.');
            } else {
                try {
                    Yii::$app->db->transaction(function () use ($ident): void {
                        if (!$ident->disablePassword()) {
                            throw new RuntimeException('Failed to disable password');
                        }
                    });
                    $this->sendEmail($ident);
                    return $this->controller->redirect(['user/profile']);
                } catch (Throwable $e) {
                    Yii::error($e, __METHOD__);
                    $ident->refresh();
                    $errorMessage = Yii::t('app-user', 'Could not disable your password.');
                }
            }
        }

        return $this->controller->render('disable-password', [
            'user' => $ident,
            'passkeyCount' => $passkeyCount,
            'errorMessage' => $errorMessage,
        ]);
    }

    private function sendEmail(User $user): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Yii::$app->mailer
                ->compose(
                    ['text' => '@app/views/email/disable-password'],
                    ['user' => $user],
                )
                ->setFrom(Yii::$app->params['notifyEmail'])
                ->setTo([$user->email => $user->name])
                ->setSubject(Yii::t(
                    'app-email',
                    '[{site}] {name} (@{screen_name}): Disabled your password',
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
