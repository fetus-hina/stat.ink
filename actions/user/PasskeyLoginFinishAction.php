<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\WebAuthnHelper;
use app\models\LoginMethod;
use app\models\PasskeyAssertionForm;
use app\models\User;
use app\models\UserAuthKey;
use app\models\UserLoginHistory;
use app\models\UserPasskey;
use app\models\UserPasskeyUser;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function filter_var;
use function headers_sent;
use function is_string;

use const FILTER_VALIDATE_BOOLEAN;

final class PasskeyLoginFinishAction extends BaseAction
{
    public function run()
    {
        if (!Yii::$app->user->getIsGuest()) {
            throw new BadRequestHttpException('Already logged in');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $req = Yii::$app->request;
        $form = PasskeyAssertionForm::fromRequest($req, PasskeyAssertionForm::SCENARIO_LOGIN);
        if (!$form->validate()) {
            return $this->failure('invalid_params');
        }

        $rememberMe = (bool)filter_var(
            $req->post('remember_me'),
            FILTER_VALIDATE_BOOLEAN,
        );

        $challengeB64 = Yii::$app->session->get(WebAuthnHelper::SESSION_KEY_LOGIN_CHALLENGE);
        if (!is_string($challengeB64) || $challengeB64 === '') {
            return $this->failure('no_challenge');
        }
        Yii::$app->session->remove(WebAuthnHelper::SESSION_KEY_LOGIN_CHALLENGE);

        $passkeyUser = UserPasskeyUser::findOne(['user_handle' => $form->user_handle]);
        if (!$passkeyUser) {
            return $this->failure('unknown_user_handle');
        }

        $passkey = UserPasskey::findOne([
            'credential_id' => $form->credential_id,
            'user_id' => $passkeyUser->user_id,
        ]);
        if (!$passkey) {
            return $this->failure('unknown_credential');
        }

        $user = User::findOne(['id' => $passkeyUser->user_id]);
        if (!$user) {
            return $this->failure('user_not_found');
        }

        if (!$form->verify($passkey, $challengeB64)) {
            return $this->failure((string)$form->errorCode, $form->errorMessage);
        }

        $appUser = Yii::$app->user;
        $appUser->on(
            \yii\web\User::EVENT_AFTER_LOGIN,
            function () use ($user): void {
                UserLoginHistory::login($user, LoginMethod::METHOD_PASSKEY);
                User::onLogin($user, LoginMethod::METHOD_PASSKEY);
            },
        );

        if (!headers_sent()) {
            Yii::$app->session->regenerateID(true);
        }

        $loggedIn = $appUser->login(
            $user,
            $rememberMe ? UserAuthKey::VALID_PERIOD : 0,
        );
        if (!$loggedIn) {
            return $this->failure('login_failed');
        }

        return [
            'result' => true,
            'screen_name' => $user->screen_name,
        ];
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
