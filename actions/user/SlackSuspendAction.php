<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Yii;
use app\components\helpers\db\Now;
use app\models\Slack;
use app\models\SlackSuspendForm;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

class SlackSuspendAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $req = Yii::$app->request;
        $form = SlackSuspendForm::validateData([
            'id' => $req->post('id'),
            'suspend' => $req->post('suspend'),
        ]);
        if ($form->hasErrors()) {
            throw new BadRequestHttpException('Bad Request');
        }

        $model = Slack::findOne([
            'id' => $form->id,
            'user_id' => $ident->id,
        ]);
        if (!$model) {
            throw new BadRequestHttpException('Bad Request');
        }

        $resp = Yii::$app->response;
        $resp->format = 'json';
        $model->suspended = ($form->suspend === 'yes');
        $model->updated_at = new Now();
        return [
            'result' => $model->save(),
        ];
    }
}
