<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\PasskeyChallengeStore;
use app\components\helpers\PasskeyReauth;
use app\components\helpers\PasskeyResponse;
use app\models\PasskeyAssertionForm;
use app\models\PasskeyChallenge;
use app\models\UserPasskey;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

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

        Yii::$app->session->remove(PasskeyReauth::SESSION_KEY_STATE);

        // The challenge is single-use; take it before anything can fail
        $challenge = PasskeyChallengeStore::take(PasskeyChallenge::PURPOSE_REAUTH, (int)$ident->id);
        if (!$challenge) {
            return PasskeyResponse::failure('no_challenge');
        }

        $form = PasskeyAssertionForm::fromRequest(
            Yii::$app->request,
            PasskeyAssertionForm::SCENARIO_REAUTH,
        );
        if (!$form->validate()) {
            return PasskeyResponse::failure('invalid_params');
        }

        $passkey = UserPasskey::findOne([
            'credential_id' => $form->credential_id,
            'user_id' => $ident->id,
        ]);
        if (!$passkey) {
            return PasskeyResponse::failure('unknown_credential');
        }

        if (!$form->verify($passkey, $challenge->challenge)) {
            return PasskeyResponse::failure((string)$form->errorCode);
        }

        PasskeyReauth::markVerified((int)$ident->id);

        return ['result' => true];
    }
}
