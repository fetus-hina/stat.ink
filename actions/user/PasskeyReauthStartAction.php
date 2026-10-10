<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\PasskeyReauth;
use app\components\helpers\WebAuthnHelper;
use app\models\UserPasskey;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function array_map;

final class PasskeyReauthStartAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $credentialIds = array_map(
            fn (string $id): string => WebAuthnHelper::base64UrlDecode($id),
            ArrayHelper::getColumn(
                UserPasskey::find()->andWhere(['user_id' => $ident->id])->all(),
                'credential_id',
            ),
        );
        if (!$credentialIds) {
            return ['result' => false, 'error' => 'no_passkey'];
        }

        $webAuthn = WebAuthnHelper::create();
        $args = $webAuthn->getGetArgs(
            credentialIds: $credentialIds,
            timeout: 60,
            requireUserVerification: 'required',
        );

        Yii::$app->session->set(
            PasskeyReauth::SESSION_KEY_CHALLENGE,
            WebAuthnHelper::base64UrlEncode($webAuthn->getChallenge()->getBinaryString()),
        );

        return $args;
    }
}
