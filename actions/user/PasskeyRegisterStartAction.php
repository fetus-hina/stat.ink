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
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction as BaseAction;

use function date;

final class PasskeyRegisterStartAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $passkeyUser = $this->ensurePasskeyUser($ident->id);
        if (!$passkeyUser) {
            throw new ServerErrorHttpException('Failed to prepare passkey user');
        }

        $excludeCredentialIds = [];
        foreach (UserPasskey::find()->andWhere(['user_id' => $ident->id])->asArray()->all() as $row) {
            $excludeCredentialIds[] = WebAuthnHelper::base64UrlDecode($row['credential_id']);
        }

        $webAuthn = WebAuthnHelper::create();
        $createArgs = $webAuthn->getCreateArgs(
            userId: WebAuthnHelper::base64UrlDecode($passkeyUser->user_handle),
            userName: $ident->screen_name,
            userDisplayName: $ident->name,
            timeout: 60,
            requireResidentKey: 'preferred',
            requireUserVerification: 'preferred',
            excludeCredentialIds: $excludeCredentialIds,
        );

        Yii::$app->session->set(
            WebAuthnHelper::SESSION_KEY_CHALLENGE,
            WebAuthnHelper::base64UrlEncode($webAuthn->getChallenge()->getBinaryString()),
        );

        return $createArgs;
    }

    private function ensurePasskeyUser(int $userId): ?UserPasskeyUser
    {
        $model = UserPasskeyUser::findOne(['user_id' => $userId]);
        if ($model) {
            return $model;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now = date('Y-m-d\TH:i:sP');
            $model = Yii::createObject([
                'class' => UserPasskeyUser::class,
                'user_id' => $userId,
                'user_handle' => WebAuthnHelper::generateUserHandleBase64(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (!$model->save()) {
                $transaction->rollback();
                return null;
            }
            $transaction->commit();
            return $model;
        } catch (Throwable $e) {
            $transaction->rollback();
            return null;
        }
    }
}
