<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
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

        return ['result' => (bool)$model->delete()];
    }
}
