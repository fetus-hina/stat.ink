<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Throwable;
use Yii;
use app\components\helpers\UserNotifier;
use app\components\helpers\WebAuthnHelper;
use app\models\PasskeyAttestationForm;
use app\models\UserPasskeyUser;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function is_string;

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

        // The challenge is single-use; consume it before anything can fail
        $session = Yii::$app->session;
        $challengeB64 = $session->get(WebAuthnHelper::SESSION_KEY_CHALLENGE);
        $session->remove(WebAuthnHelper::SESSION_KEY_CHALLENGE);
        if (!is_string($challengeB64) || $challengeB64 === '') {
            throw new BadRequestHttpException('No challenge in session');
        }

        $form = PasskeyAttestationForm::fromRequest(Yii::$app->request);
        if (!$form->validate()) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $passkeyUser = UserPasskeyUser::findOne(['user_id' => $ident->id]);
        if (!$passkeyUser) {
            throw new BadRequestHttpException('Passkey user not initialized');
        }

        if (!$form->verify($challengeB64)) {
            return [
                'result' => false,
                'error' => $form->errorCode,
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $form->buildPasskey((int)$ident->id);
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
            ];
        }

        UserNotifier::passkeyRegistered($ident, (string)$model->nickname);

        return [
            'result' => true,
            'id' => $model->id,
        ];
    }
}
