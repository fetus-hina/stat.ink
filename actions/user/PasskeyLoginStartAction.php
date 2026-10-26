<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Override;
use Yii;
use app\components\helpers\PasskeyChallengeStore;
use app\components\helpers\WebAuthnHelper;
use app\models\PasskeyChallenge;
use stdClass;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

final class PasskeyLoginStartAction extends BaseAction
{
    #[Override]
    public function run(): stdClass
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

        PasskeyChallengeStore::issue(
            PasskeyChallenge::PURPOSE_LOGIN,
            $webAuthn->getChallenge()->getBinaryString(),
            null,
        );

        return $args;
    }
}
