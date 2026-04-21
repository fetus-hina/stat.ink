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
use app\models\User;
use app\models\UserPasskey;
use app\models\UserPasskeyUser;
use lbuchs\WebAuthn\WebAuthnException;
use yii\base\DynamicModel;
use yii\db\ArrayExpression;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function array_filter;
use function array_values;
use function date;
use function in_array;
use function is_array;
use function is_string;
use function trim;

final class PasskeyRegisterFinishAction extends BaseAction
{
    private const ALLOWED_TRANSPORTS = ['usb', 'nfc', 'ble', 'internal', 'hybrid', 'smart-card'];

    // Rough upper bounds for the base64url-encoded payload:
    //   - clientDataJSON is a tiny JSON object (a few hundred bytes)
    //   - attestationObject depends on the attestation format; even packed/tpm rarely exceeds a few KB
    private const MAX_CLIENT_DATA_LEN = 4096;
    private const MAX_ATTESTATION_OBJECT_LEN = 16384;

    private const BASE64URL_PATTERN = '/\A[A-Za-z0-9_-]+\z/';

    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $req = Yii::$app->request;
        $form = DynamicModel::validateData(
            [
                'client_data_json' => (string)$req->post('client_data_json', ''),
                'attestation_object' => (string)$req->post('attestation_object', ''),
                'nickname' => trim((string)$req->post('nickname', '')),
            ],
            [
                [['client_data_json', 'attestation_object', 'nickname'], 'required'],
                [['nickname'], 'string', 'max' => 64],
                [['client_data_json'], 'string', 'max' => self::MAX_CLIENT_DATA_LEN],
                [['attestation_object'], 'string', 'max' => self::MAX_ATTESTATION_OBJECT_LEN],
                [['client_data_json', 'attestation_object'], 'match',
                    'pattern' => self::BASE64URL_PATTERN,
                ],
            ],
        );
        if ($form->hasErrors()) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $transports = $this->normalizeTransports($req->post('transports'));

        $challengeB64 = Yii::$app->session->get(WebAuthnHelper::SESSION_KEY_CHALLENGE);
        if (!is_string($challengeB64) || $challengeB64 === '') {
            throw new BadRequestHttpException('No challenge in session');
        }

        $passkeyUser = UserPasskeyUser::findOne(['user_id' => $ident->id]);
        if (!$passkeyUser) {
            throw new BadRequestHttpException('Passkey user not initialized');
        }

        try {
            $webAuthn = WebAuthnHelper::create();
            $data = $webAuthn->processCreate(
                clientDataJSON: WebAuthnHelper::base64UrlDecode($form->client_data_json),
                attestationObject: WebAuthnHelper::base64UrlDecode($form->attestation_object),
                challenge: WebAuthnHelper::base64UrlDecode($challengeB64),
                requireUserVerification: false,
                requireUserPresent: true,
                failIfRootMismatch: false,
            );
        } catch (WebAuthnException $e) {
            Yii::$app->session->remove(WebAuthnHelper::SESSION_KEY_CHALLENGE);
            return [
                'result' => false,
                'error' => 'verification_failed',
                'message' => $e->getMessage(),
            ];
        }

        Yii::$app->session->remove(WebAuthnHelper::SESSION_KEY_CHALLENGE);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now = date('Y-m-d\TH:i:sP');
            $model = Yii::createObject([
                'class' => UserPasskey::class,
                'user_id' => $ident->id,
                'credential_id' => WebAuthnHelper::base64UrlEncode($data->credentialId),
                'public_key' => (string)$data->credentialPublicKey,
                'sign_count' => (int)$data->signatureCounter,
                'aaguid' => WebAuthnHelper::binaryToUuidString($data->AAGUID),
                'attestation_format' => (string)$data->attestationFormat,
                'transports' => new ArrayExpression($transports, 'text', 1),
                'backup_eligible' => (bool)($data->isBackupEligible ?? false),
                'backup_state' => (bool)($data->isBackedUp ?? false),
                'nickname' => $form->nickname,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (!$model->save()) {
                $transaction->rollback();
                return [
                    'result' => false,
                    'error' => 'save_failed',
                    'errors' => $model->getErrors(),
                ];
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollback();
            Yii::error($e, __METHOD__);
            return [
                'result' => false,
                'error' => 'exception',
                'message' => $e->getMessage(),
            ];
        }

        $this->sendEmail($ident, $model);

        return [
            'result' => true,
            'id' => $model->id,
        ];
    }

    private function sendEmail(User $user, UserPasskey $passkey): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Yii::$app->mailer
                ->compose(
                    ['text' => '@app/views/email/register-passkey'],
                    ['user' => $user, 'nickname' => $passkey->nickname],
                )
                ->setFrom(Yii::$app->params['notifyEmail'])
                ->setTo([$user->email => $user->name])
                ->setSubject(Yii::t(
                    'app-email',
                    '[{site}] {name} (@{screen_name}): Passkey registered',
                    [
                        'name' => $user->name,
                        'screen_name' => $user->screen_name,
                        'site' => Yii::$app->name,
                    ],
                    $user->emailLang->lang ?? 'en-US',
                ))
                ->send();
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
        }
    }

    /**
     * @return string[]
     */
    private function normalizeTransports(mixed $transports): array
    {
        if (!is_array($transports)) {
            return [];
        }

        return array_values(array_filter(
            $transports,
            fn ($v): bool => is_string($v) && in_array($v, self::ALLOWED_TRANSPORTS, true),
        ));
    }
}
