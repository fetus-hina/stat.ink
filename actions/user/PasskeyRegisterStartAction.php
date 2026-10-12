<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\PasskeyChallengeStore;
use app\components\helpers\WebAuthnHelper;
use app\models\PasskeyChallenge;
use app\models\UserPasskey;
use app\models\UserPasskeyUser;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction as BaseAction;

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

        $passkeyUser = UserPasskeyUser::findOrCreate((int)$ident->id);
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
            requireResidentKey: 'required',
            requireUserVerification: 'required',
            excludeCredentialIds: $excludeCredentialIds,
        );

        PasskeyChallengeStore::issue(
            PasskeyChallenge::PURPOSE_REGISTER,
            $webAuthn->getChallenge()->getBinaryString(),
            (int)$ident->id,
        );

        return $createArgs;
    }
}
