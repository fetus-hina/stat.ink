<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\PasskeyReauth;
use app\models\PasskeyAssertionForm;
use app\models\UserPasskey;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function is_string;

/**
 * Re-authenticates the logged-in user with one of their own passkeys
 *
 * On success, the session is marked as "recently re-authenticated" for a short
 * period. Sensitive actions check it with PasskeyReauth::consume().
 */
final class PasskeyReauthFinishAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $session = Yii::$app->session;
        $session->remove(PasskeyReauth::SESSION_KEY_STATE);

        $form = PasskeyAssertionForm::fromRequest(
            Yii::$app->request,
            PasskeyAssertionForm::SCENARIO_REAUTH,
        );
        if (!$form->validate()) {
            return $this->failure('invalid_params');
        }

        $challengeB64 = $session->get(PasskeyReauth::SESSION_KEY_CHALLENGE);
        if (!is_string($challengeB64) || $challengeB64 === '') {
            return $this->failure('no_challenge');
        }
        $session->remove(PasskeyReauth::SESSION_KEY_CHALLENGE);

        $passkey = UserPasskey::findOne([
            'credential_id' => $form->credential_id,
            'user_id' => $ident->id,
        ]);
        if (!$passkey) {
            return $this->failure('unknown_credential');
        }

        if (!$form->verify($passkey, $challengeB64)) {
            return $this->failure((string)$form->errorCode, $form->errorMessage);
        }

        PasskeyReauth::markVerified((int)$ident->id);

        return ['result' => true];
    }

    /**
     * @return array{result: false, error: string, message?: string}
     */
    private function failure(string $error, ?string $message = null): array
    {
        $out = ['result' => false, 'error' => $error];
        if ($message !== null) {
            $out['message'] = $message;
        }
        return $out;
    }
}
