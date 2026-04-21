<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Throwable;
use Yii;
use app\components\helpers\WebAuthnHelper;
use app\models\LoginMethod;
use app\models\User;
use app\models\UserAuthKey;
use app\models\UserLoginHistory;
use app\models\UserPasskey;
use app\models\UserPasskeyUser;
use lbuchs\WebAuthn\WebAuthnException;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function date;
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
        $credentialIdB64 = (string)$req->post('credential_id', '');
        $clientDataJsonB64 = (string)$req->post('client_data_json', '');
        $authenticatorDataB64 = (string)$req->post('authenticator_data', '');
        $signatureB64 = (string)$req->post('signature', '');
        $userHandleB64 = (string)$req->post('user_handle', '');
        $rememberMe = (bool)filter_var(
            $req->post('remember_me'),
            FILTER_VALIDATE_BOOLEAN,
        );

        if (
            $credentialIdB64 === ''
            || $clientDataJsonB64 === ''
            || $authenticatorDataB64 === ''
            || $signatureB64 === ''
            || $userHandleB64 === ''
        ) {
            return $this->failure('invalid_params');
        }

        $challengeB64 = Yii::$app->session->get(WebAuthnHelper::SESSION_KEY_LOGIN_CHALLENGE);
        if (!is_string($challengeB64) || $challengeB64 === '') {
            return $this->failure('no_challenge');
        }
        Yii::$app->session->remove(WebAuthnHelper::SESSION_KEY_LOGIN_CHALLENGE);

        $passkeyUser = UserPasskeyUser::findOne(['user_handle' => $userHandleB64]);
        if (!$passkeyUser) {
            return $this->failure('unknown_user_handle');
        }

        $passkey = UserPasskey::findOne([
            'credential_id' => $credentialIdB64,
            'user_id' => $passkeyUser->user_id,
        ]);
        if (!$passkey) {
            return $this->failure('unknown_credential');
        }

        $user = User::findOne(['id' => $passkeyUser->user_id]);
        if (!$user) {
            return $this->failure('user_not_found');
        }

        try {
            $webAuthn = WebAuthnHelper::create();
            $webAuthn->processGet(
                clientDataJSON: WebAuthnHelper::base64UrlDecode($clientDataJsonB64),
                authenticatorData: WebAuthnHelper::base64UrlDecode($authenticatorDataB64),
                signature: WebAuthnHelper::base64UrlDecode($signatureB64),
                credentialPublicKey: $passkey->public_key,
                challenge: WebAuthnHelper::base64UrlDecode($challengeB64),
                prevSignatureCnt: (int)$passkey->sign_count,
                requireUserVerification: false,
                requireUserPresent: true,
            );
        } catch (WebAuthnException $e) {
            return $this->failure('verification_failed', $e->getMessage());
        }

        $newSignCount = (int)$webAuthn->getSignatureCounter();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now = date('Y-m-d\TH:i:sP');
            $passkey->sign_count = $newSignCount;
            $passkey->last_used_at = $now;
            $passkey->updated_at = $now;
            if (!$passkey->save()) {
                $transaction->rollback();
                return $this->failure('save_failed');
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollback();
            Yii::error($e, __METHOD__);
            return $this->failure('exception', $e->getMessage());
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
