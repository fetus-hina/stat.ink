<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Throwable;
use Yii;
use app\components\helpers\PasskeyChallengeStore;
use app\components\helpers\PasskeyResponse;
use app\components\helpers\UserNotifier;
use app\models\PasskeyAttestationForm;
use app\models\PasskeyChallenge;
use app\models\UserPasskeyUser;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

final class PasskeyRegisterFinishAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';

        // The challenge is single-use; take it before anything can fail
        $challenge = PasskeyChallengeStore::take(PasskeyChallenge::PURPOSE_REGISTER, (int)$ident->id);
        if (!$challenge) {
            return PasskeyResponse::failure('no_challenge');
        }

        $form = PasskeyAttestationForm::fromRequest(Yii::$app->request);
        if (!$form->validate()) {
            return PasskeyResponse::failure('invalid_params');
        }

        $passkeyUser = UserPasskeyUser::findOne(['user_id' => $ident->id]);
        if (!$passkeyUser) {
            return PasskeyResponse::failure('passkey_user_not_found');
        }

        if (!$form->verify($challenge->challenge)) {
            return PasskeyResponse::failure((string)$form->errorCode);
        }

        try {
            $model = $form->buildPasskey((int)$ident->id);
            if (!$model->save()) {
                Yii::warning($model->getErrors(), __METHOD__);
                return PasskeyResponse::failure('save_failed');
            }
        } catch (Throwable $e) {
            Yii::error($e, __METHOD__);
            return PasskeyResponse::failure('exception');
        }

        UserNotifier::passkeyRegistered($ident, (string)$model->nickname);

        return [
            'result' => true,
            'id' => $model->id,
        ];
    }
}
