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
use app\models\UserPasskey;
use app\models\UserPasskeyUser;
use lbuchs\WebAuthn\WebAuthnException;
use yii\db\ArrayExpression;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function array_filter;
use function array_values;
use function date;
use function in_array;
use function is_array;
use function is_string;
use function mb_strlen;
use function trim;

final class PasskeyRegisterFinishAction extends BaseAction
{
    private const ALLOWED_TRANSPORTS = ['usb', 'nfc', 'ble', 'internal', 'hybrid', 'smart-card'];

    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $req = Yii::$app->request;
        $clientDataJsonB64 = (string)$req->post('client_data_json', '');
        $attestationObjectB64 = (string)$req->post('attestation_object', '');
        $nickname = trim((string)$req->post('nickname', ''));
        $transports = $this->normalizeTransports($req->post('transports'));

        if ($clientDataJsonB64 === '' || $attestationObjectB64 === '' || $nickname === '') {
            throw new BadRequestHttpException('Invalid parameters');
        }

        if (mb_strlen($nickname) > 64) {
            throw new BadRequestHttpException('Nickname too long');
        }

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
                clientDataJSON: WebAuthnHelper::base64UrlDecode($clientDataJsonB64),
                attestationObject: WebAuthnHelper::base64UrlDecode($attestationObjectB64),
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
                'nickname' => $nickname,
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

        return [
            'result' => true,
            'id' => $model->id,
        ];
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
