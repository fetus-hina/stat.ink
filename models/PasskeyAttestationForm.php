<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use DateTimeImmutable;
use LogicException;
use Override;
use Yii;
use app\components\helpers\PasskeyNickname;
use app\components\helpers\WebAuthnHelper;
use lbuchs\WebAuthn\WebAuthnException;
use stdClass;
use yii\base\Model;
use yii\db\ArrayExpression;
use yii\web\Request;

use function is_string;
use function trim;

/**
 * Validates and verifies a WebAuthn attestation (navigator.credentials.create())
 */
final class PasskeyAttestationForm extends Model
{
    // Rough upper bounds for the base64url-encoded payload:
    //   - clientDataJSON is a tiny JSON object (a few hundred bytes)
    //   - attestationObject depends on the attestation format; even packed/tpm rarely exceeds a few KB
    private const MAX_CLIENT_DATA_LEN = 4096;
    private const MAX_ATTESTATION_OBJECT_LEN = 16384;

    private const BASE64URL_PATTERN = '/\A[A-Za-z0-9_-]+\z/';

    public string $client_data_json = '';
    public string $attestation_object = '';
    public string $nickname = '';

    /**
     * @var list<string>
     */
    public array $transports = [];

    /**
     * Machine-readable reason of the last verify() failure
     */
    public ?string $errorCode = null;

    private ?stdClass $data = null;

    public static function fromRequest(Request $request): self
    {
        $form = new self();
        foreach (['client_data_json', 'attestation_object', 'nickname'] as $attr) {
            $value = $request->post($attr);
            $form->$attr = is_string($value) ? trim($value) : '';
        }
        $form->transports = WebAuthnHelper::normalizeTransports($request->post('transports'));
        return $form;
    }

    #[Override]
    public function rules()
    {
        return [
            [['client_data_json', 'attestation_object'], 'required'],
            [['nickname'], 'string', 'max' => PasskeyNickname::MAX_NICKNAME_LENGTH],
            [['client_data_json'], 'string', 'max' => self::MAX_CLIENT_DATA_LEN],
            [['attestation_object'], 'string', 'max' => self::MAX_ATTESTATION_OBJECT_LEN],
            [['client_data_json', 'attestation_object'], 'match',
                'pattern' => self::BASE64URL_PATTERN,
            ],
        ];
    }

    /**
     * Verifies the attestation against the challenge
     *
     * On failure, $errorCode is set. The details are logged, not exposed to the client.
     */
    public function verify(string $challengeB64): bool
    {
        $this->errorCode = null;
        $this->data = null;

        try {
            $webAuthn = WebAuthnHelper::create();
            $this->data = $webAuthn->processCreate(
                clientDataJSON: WebAuthnHelper::base64UrlDecode($this->client_data_json),
                attestationObject: WebAuthnHelper::base64UrlDecode($this->attestation_object),
                challenge: WebAuthnHelper::base64UrlDecode($challengeB64),
                requireUserVerification: true,
                requireUserPresent: true,
                failIfRootMismatch: false,
            );
        } catch (WebAuthnException $e) {
            Yii::warning($e->getMessage(), __METHOD__);
            $this->errorCode = 'verification_failed';
            return false;
        }

        return true;
    }

    /**
     * Builds an unsaved UserPasskey from the verified attestation
     */
    public function buildPasskey(int $userId): UserPasskey
    {
        $data = $this->data;
        if ($data === null) {
            throw new LogicException('The attestation has not been verified yet');
        }

        $aaguid = WebAuthnHelper::binaryToUuidString($data->AAGUID);
        $now = new DateTimeImmutable();

        return Yii::createObject([
            'class' => UserPasskey::class,
            'user_id' => $userId,
            'credential_id' => WebAuthnHelper::base64UrlEncode($data->credentialId),
            'public_key' => (string)$data->credentialPublicKey,
            'sign_count' => (int)$data->signatureCounter,
            'aaguid' => $aaguid,
            'attestation_format' => (string)$data->attestationFormat,
            'transports' => new ArrayExpression($this->transports, 'text', 1),
            'backup_eligible' => (bool)($data->isBackupEligible ?? false),
            'backup_state' => (bool)($data->isBackedUp ?? false),
            'nickname' => $this->nickname !== ''
                ? $this->nickname
                : $this->buildDefaultNickname($aaguid, $now),
        ]);
    }

    private function buildDefaultNickname(string $aaguid, DateTimeImmutable $now): string
    {
        $aaguidName = null;
        if (PasskeyNickname::isKnownAaguid($aaguid)) {
            $row = PasskeyAaguid::find()
                ->select(['name'])
                ->where(['aaguid' => $aaguid])
                ->scalar();
            if (is_string($row)) {
                $aaguidName = $row;
            }
        }

        return PasskeyNickname::buildDefault(
            $aaguid,
            $aaguidName,
            $now,
            Yii::t('app-passkey', 'Passkey ({date})'),
        );
    }
}
