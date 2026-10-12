<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\WebAuthnHelper;
use app\models\RegisterForm;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

/**
 * Starts a new user registration with a passkey (no password)
 *
 * The user is not created here; the validated profile is kept in the session
 * together with the challenge, and the account is created by
 * PasskeySignupFinishAction once the passkey has been verified.
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

        $session = Yii::$app->session;
        $session->remove(WebAuthnHelper::SESSION_KEY_SIGNUP);

        $form = new RegisterForm(['scenario' => RegisterForm::SCENARIO_PASSKEY]);
        $form->load(Yii::$app->request->post());
        if (!$form->validate()) {
            return self::validationFailure($form);
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

        $session->set(WebAuthnHelper::SESSION_KEY_SIGNUP, [
            'challenge' => WebAuthnHelper::base64UrlEncode($webAuthn->getChallenge()->getBinaryString()),
            'user_handle' => $userHandle,
            'screen_name' => $screenName,
            'name' => $name,
        ]);

        return [
            'result' => true,
            'options' => $createArgs,
        ];
    }

    /**
     * Returns the errors keyed by the input ID, as yiiActiveForm('updateMessages') expects
     *
     * @return array{result: false, error: string, errors: array<string, string[]>}
     */
    public static function validationFailure(RegisterForm $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $attr => $messages) {
            $errors[Html::getInputId($form, $attr)] = $messages;
        }

        return [
            'result' => false,
            'error' => 'invalid_params',
            'errors' => $errors,
        ];
    }
}
