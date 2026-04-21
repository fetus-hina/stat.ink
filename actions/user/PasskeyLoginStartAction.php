<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\components\helpers\WebAuthnHelper;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

final class PasskeyLoginStartAction extends BaseAction
{
    public function run()
    {
        if (!Yii::$app->user->getIsGuest()) {
            throw new BadRequestHttpException('Already logged in');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        $webAuthn = WebAuthnHelper::create();
        $args = $webAuthn->getGetArgs(
            credentialIds: [],
            timeout: 60,
            requireUserVerification: 'preferred',
        );

        Yii::$app->session->set(
            WebAuthnHelper::SESSION_KEY_LOGIN_CHALLENGE,
            WebAuthnHelper::base64UrlEncode($webAuthn->getChallenge()->getBinaryString()),
        );

        return $args;
    }
}
