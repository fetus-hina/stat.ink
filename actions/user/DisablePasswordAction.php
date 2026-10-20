<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Override;
use RuntimeException;
use Throwable;
use Yii;
use app\components\helpers\PasskeyReauth;
use app\components\helpers\UserNotifier;
use app\models\User;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

/**
 * Disables (forgets) the password of a user who has at least one passkey
 */
final class DisablePasswordAction extends BaseAction
{
    #[Override]
    public function run(): string|Response
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident instanceof User) {
            return $this->controller->redirect(['user/login']);
        }

        if (!$ident->canDisablePassword()) {
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
                    UserNotifier::passwordDisabled($ident);
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
            'passkeyCount' => (int)$ident->getUserPasskeys()->count(),
            'errorMessage' => $errorMessage,
        ]);
    }
}
