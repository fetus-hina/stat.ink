<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\PasskeyChallengeStore;
use app\components\helpers\PasskeyResponse;
use app\components\helpers\WebAuthnHelper;
use app\models\PasskeyChallenge;
use app\models\RegisterForm;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

/**
 * Starts a new user registration with a passkey (no password)
 *
 * The user is not created here; the validated profile is kept together with
 * the challenge, and the account is created by PasskeySignupFinishAction once
 * the passkey has been verified.
 */
final class PasskeySignupStartAction extends BaseAction
{
    public function run()
    {
        if (!Yii::$app->user->getIsGuest()) {
            throw new BadRequestHttpException('Already logged in');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $form = new RegisterForm(['scenario' => RegisterForm::SCENARIO_PASSKEY]);
        $form->load(Yii::$app->request->post());
        if (!$form->validate()) {
            return PasskeyResponse::validationFailure($form);
        }

        $screenName = (string)$form->screen_name;
        $name = (string)$form->name;
        $userHandle = WebAuthnHelper::generateUserHandleBase64();

        $webAuthn = WebAuthnHelper::create();
        $createArgs = $webAuthn->getCreateArgs(
            userId: WebAuthnHelper::base64UrlDecode($userHandle),
            userName: $screenName,
            userDisplayName: $name !== '' ? $name : $screenName,
            timeout: 60,
            requireResidentKey: 'required',
            requireUserVerification: 'required',
        );

        PasskeyChallengeStore::issue(
            PasskeyChallenge::PURPOSE_SIGNUP,
            $webAuthn->getChallenge()->getBinaryString(),
            null,
            [
                'user_handle' => $userHandle,
                'screen_name' => $screenName,
                'name' => $name,
            ],
        );

        return [
            'result' => true,
            'options' => $createArgs,
        ];
    }
}
