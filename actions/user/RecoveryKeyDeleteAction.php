<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use DateTime;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\UserPasswordRecoveryKey;
use app\models\UserPasswordRecoveryKeyRevokeReason;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function date;

final class RecoveryKeyDeleteAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
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

        $model = UserPasswordRecoveryKey::findOne([
            'id' => (int)$form->id,
            'user_id' => $ident->id,
            'used_at' => null,
            'revoked_at' => null,
        ]);
        if ($model) {
            $model->revoked_at = date(
                DateTime::ATOM,
                TypeHelper::int($_SERVER['REQUEST_TIME']),
            );
            $model->revoked_reason = UserPasswordRecoveryKeyRevokeReason::REASON_USER_REQUEST;
            $model->save();
        }

        return $this->controller->redirect(['user/recovery-key']);
    }
}
