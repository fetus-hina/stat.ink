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
use app\models\PasskeyDeleteForm;
use app\models\UserPasskey;
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
        $form = PasskeyDeleteForm::validateData([
            'id' => $req->post('id'),
        ]);
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

        if (!$ident->canDeletePasskey()) {
            return [
                'result' => false,
                'error' => 'last_passkey',
                'message' => Yii::t(
                    'app-passkey',
                    'You cannot delete your last passkey while your password is disabled.',
                ),
            ];
        }

        $nickname = (string)$model->nickname;
        try {
            $deleted = (bool)$model->delete();
        } catch (Throwable $e) {
            // The database rejects deleting the last passkey of a user who has
            // no password (e.g., concurrent deletion)
            Yii::error($e, __METHOD__);
            return ['result' => false];
        }
        if ($deleted) {
            UserNotifier::passkeyDeleted($ident, $nickname);
        }

        return ['result' => $deleted];
    }
}
