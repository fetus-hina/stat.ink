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
use app\models\User;
use app\models\UserPasskey;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

final class PasskeyDeleteAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $req = Yii::$app->request;
        $form = DynamicModel::validateData(
            [
                'id' => $req->post('id'),
            ],
            [
                [['id'], 'required'],
                [['id'], 'integer'],
            ],
        );
        if ($form->hasErrors()) {
            throw new BadRequestHttpException('Bad Request');
        }

        $model = UserPasskey::findOne([
            'id' => (int)$form->id,
            'user_id' => $ident->id,
        ]);

        $resp = Yii::$app->response;
        $resp->format = 'json';

        if (!$model) {
            return ['result' => false];
        }

        $nickname = (string)$model->nickname;
        $deleted = (bool)$model->delete();
        if ($deleted) {
            $this->sendEmail($ident, $nickname);
        }

        return ['result' => $deleted];
    }

    private function sendEmail(User $user, string $nickname): void
    {
        if (!$user->email) {
            return;
        }

        try {
            Yii::$app->mailer
                ->compose(
                    ['text' => '@app/views/email/delete-passkey'],
                    ['user' => $user, 'nickname' => $nickname],
                )
                ->setFrom(Yii::$app->params['notifyEmail'])
                ->setTo([$user->email => $user->name])
                ->setSubject(Yii::t(
                    'app-email',
                    '[{site}] {name} (@{screen_name}): Passkey deleted',
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
