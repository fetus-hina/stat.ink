<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user;

use Throwable;
use Yii;
use app\components\helpers\WebAuthnHelper;
use app\models\PasskeyAttestationForm;
use app\models\User;
use app\models\UserPasskey;
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

        $form = PasskeyAttestationForm::fromRequest(Yii::$app->request);
        if (!$form->validate()) {
            throw new BadRequestHttpException('Invalid parameters');
        }

        $challengeB64 = Yii::$app->session->get(WebAuthnHelper::SESSION_KEY_CHALLENGE);
        if (!is_string($challengeB64) || $challengeB64 === '') {
            throw new BadRequestHttpException('No challenge in session');
        }

        $passkeyUser = UserPasskeyUser::findOne(['user_id' => $ident->id]);
        if (!$passkeyUser) {
            throw new BadRequestHttpException('Passkey user not initialized');
        }

        $verified = $form->verify($challengeB64);
        Yii::$app->session->remove(WebAuthnHelper::SESSION_KEY_CHALLENGE);
        if (!$verified) {
            return [
                'result' => false,
                'error' => $form->errorCode,
                'message' => $form->errorMessage,
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
}
