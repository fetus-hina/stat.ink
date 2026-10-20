<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Override;
use Yii;
use app\components\helpers\PasskeyChallengeStore;
use app\components\helpers\PasskeyResponse;
use app\models\LoginMethod;
use app\models\PasskeyAssertionForm;
use app\models\PasskeyChallenge;
use app\models\User;
use app\models\UserAuthKey;
use app\models\UserLoginHistory;
use app\models\UserPasskey;
use app\models\UserPasskeyUser;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function filter_var;
use function headers_sent;

use const FILTER_VALIDATE_BOOLEAN;

final class PasskeyLoginFinishAction extends BaseAction
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function run(): array
    {
        if (!Yii::$app->user->getIsGuest()) {
            throw new BadRequestHttpException('Already logged in');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        // The challenge is single-use; take it before anything can fail
        $challenge = PasskeyChallengeStore::take(PasskeyChallenge::PURPOSE_LOGIN, null);
        if (!$challenge) {
            return PasskeyResponse::failure('no_challenge');
        }

        $req = Yii::$app->request;
        $form = PasskeyAssertionForm::fromRequest($req, PasskeyAssertionForm::SCENARIO_LOGIN);
        if (!$form->validate()) {
            return PasskeyResponse::failure('invalid_params');
        }

        $rememberMe = (bool)filter_var(
            $req->post('remember_me'),
            FILTER_VALIDATE_BOOLEAN,
        );

        $passkeyUser = UserPasskeyUser::findOne(['user_handle' => $form->user_handle]);
        if (!$passkeyUser) {
            return PasskeyResponse::failure('unknown_user_handle');
        }

        $passkey = UserPasskey::findOne([
            'credential_id' => $form->credential_id,
            'user_id' => $passkeyUser->user_id,
        ]);
        if (!$passkey) {
            return PasskeyResponse::failure('unknown_credential');
        }

        $user = User::findOne(['id' => $passkeyUser->user_id]);
        if (!$user) {
            return PasskeyResponse::failure('user_not_found');
        }

        if (!$form->verify($passkey, $challenge->challenge)) {
            return PasskeyResponse::failure((string)$form->errorCode);
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
            return PasskeyResponse::failure('login_failed');
        }

        return [
            'result' => true,
            'screen_name' => $user->screen_name,
        ];
    }
}
