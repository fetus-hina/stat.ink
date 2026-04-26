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
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function date;
use function filter_var;
use function headers_sent;
use function is_string;

use const FILTER_VALIDATE_BOOLEAN;

final class PasskeyLoginFinishAction extends BaseAction
{
    // Upper bounds for the base64url-encoded payloads (~= ceil(n * 4 / 3)):
    //   - credential id: at most 1023 raw bytes (WebAuthn spec)
    //   - user handle: we generate 64 raw bytes
    //   - client data: tiny JSON object
    //   - authenticator data: at most a few hundred bytes
    //   - signature: ECDSA/RSA/EdDSA, at most ~1 KB
    private const MAX_CREDENTIAL_ID_LEN = 1400;
    private const MAX_USER_HANDLE_LEN = 128;
    private const MAX_CLIENT_DATA_LEN = 4096;
    private const MAX_AUTHENTICATOR_DATA_LEN = 2048;
    private const MAX_SIGNATURE_LEN = 2048;

    private const BASE64URL_PATTERN = '/\A[A-Za-z0-9_-]+\z/';

    public function run()
    {
        if (!Yii::$app->user->getIsGuest()) {
            throw new BadRequestHttpException('Already logged in');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $req = Yii::$app->request;
        $form = DynamicModel::validateData(
            [
                'credential_id' => (string)$req->post('credential_id', ''),
                'client_data_json' => (string)$req->post('client_data_json', ''),
                'authenticator_data' => (string)$req->post('authenticator_data', ''),
                'signature' => (string)$req->post('signature', ''),
                'user_handle' => (string)$req->post('user_handle', ''),
            ],
            [
                [
                    ['credential_id', 'client_data_json', 'authenticator_data', 'signature', 'user_handle'],
                    'required',
                ],
                [['credential_id'], 'string', 'max' => self::MAX_CREDENTIAL_ID_LEN],
                [['user_handle'], 'string', 'max' => self::MAX_USER_HANDLE_LEN],
                [['client_data_json'], 'string', 'max' => self::MAX_CLIENT_DATA_LEN],
                [['authenticator_data'], 'string', 'max' => self::MAX_AUTHENTICATOR_DATA_LEN],
                [['signature'], 'string', 'max' => self::MAX_SIGNATURE_LEN],
                [
                    ['credential_id', 'client_data_json', 'authenticator_data', 'signature', 'user_handle'],
                    'match',
                    'pattern' => self::BASE64URL_PATTERN,
                ],
            ],
        );
        if ($form->hasErrors()) {
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

        try {
            $webAuthn = WebAuthnHelper::create();
            $webAuthn->processGet(
                clientDataJSON: WebAuthnHelper::base64UrlDecode($form->client_data_json),
                authenticatorData: WebAuthnHelper::base64UrlDecode($form->authenticator_data),
                signature: WebAuthnHelper::base64UrlDecode($form->signature),
                credentialPublicKey: $passkey->public_key,
                challenge: WebAuthnHelper::base64UrlDecode($challengeB64),
                prevSignatureCnt: (int)$passkey->sign_count,
                requireUserVerification: true,
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
