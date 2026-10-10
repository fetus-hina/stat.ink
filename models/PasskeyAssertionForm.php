<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use Throwable;
use Yii;
use app\components\helpers\WebAuthnHelper;
use lbuchs\WebAuthn\WebAuthnException;
use yii\base\Model;
use yii\web\Request;

use function date;
use function is_string;

/**
 * Validates and verifies a WebAuthn assertion (navigator.credentials.get())
 */
final class PasskeyAssertionForm extends Model
{
    public const SCENARIO_LOGIN = 'login';
    public const SCENARIO_REAUTH = 'reauth';

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

    public string $credential_id = '';
    public string $client_data_json = '';
    public string $authenticator_data = '';
    public string $signature = '';
    public string $user_handle = '';

    /**
     * Machine-readable reason of the last verify() failure
     */
    public ?string $errorCode = null;

    /**
     * Human-readable detail of the last verify() failure, if any
     */
    public ?string $errorMessage = null;

    public static function fromRequest(Request $request, string $scenario): self
    {
        $form = new self(['scenario' => $scenario]);
        foreach ($form->activeAttributes() as $attr) {
            $value = $request->post($attr);
            $form->$attr = is_string($value) ? $value : '';
        }
        return $form;
    }

    #[Override]
    public function scenarios()
    {
        $common = ['credential_id', 'client_data_json', 'authenticator_data', 'signature'];
        return [
            self::SCENARIO_LOGIN => [...$common, 'user_handle'],
            self::SCENARIO_REAUTH => $common,
        ];
    }

    #[Override]
    public function rules()
    {
        $all = ['credential_id', 'client_data_json', 'authenticator_data', 'signature', 'user_handle'];
        return [
            [$all, 'required'],
            [['credential_id'], 'string', 'max' => self::MAX_CREDENTIAL_ID_LEN],
            [['user_handle'], 'string', 'max' => self::MAX_USER_HANDLE_LEN],
            [['client_data_json'], 'string', 'max' => self::MAX_CLIENT_DATA_LEN],
            [['authenticator_data'], 'string', 'max' => self::MAX_AUTHENTICATOR_DATA_LEN],
            [['signature'], 'string', 'max' => self::MAX_SIGNATURE_LEN],
            [$all, 'match', 'pattern' => self::BASE64URL_PATTERN],
        ];
    }

    /**
     * Verifies the assertion against the passkey, and records its use
     *
     * On failure, $errorCode (and optionally $errorMessage) is set.
     */
    public function verify(UserPasskey $passkey, string $challengeB64): bool
    {
        $this->errorCode = null;
        $this->errorMessage = null;

        try {
            $webAuthn = WebAuthnHelper::create();
            $webAuthn->processGet(
                clientDataJSON: WebAuthnHelper::base64UrlDecode($this->client_data_json),
                authenticatorData: WebAuthnHelper::base64UrlDecode($this->authenticator_data),
                signature: WebAuthnHelper::base64UrlDecode($this->signature),
                credentialPublicKey: $passkey->public_key,
                challenge: WebAuthnHelper::base64UrlDecode($challengeB64),
                prevSignatureCnt: (int)$passkey->sign_count,
                requireUserVerification: true,
                requireUserPresent: true,
            );
        } catch (WebAuthnException $e) {
            return $this->fail('verification_failed', $e->getMessage());
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now = date('Y-m-d\TH:i:sP');
            $passkey->sign_count = (int)$webAuthn->getSignatureCounter();
            $passkey->last_used_at = $now;
            $passkey->updated_at = $now;
            if (!$passkey->save()) {
                $transaction->rollback();
                return $this->fail('save_failed');
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollback();
            Yii::error($e, __METHOD__);
            return $this->fail('exception', $e->getMessage());
        }

        return true;
    }

    private function fail(string $code, ?string $message = null): bool
    {
        $this->errorCode = $code;
        $this->errorMessage = $message;
        return false;
    }
}
