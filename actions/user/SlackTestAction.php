<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Yii;
use app\models\Slack;
use app\models\SlackTestForm;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class SlackTestAction extends BaseAction
{
    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function run(): array
    {
        $ident = Yii::$app->user->getIdentity();
        if (!$ident) {
            throw new BadRequestHttpException('Bad Request');
        }

        $req = Yii::$app->request;
        $form = SlackTestForm::validateData(['id' => $req->post('id')]);
        if ($form->hasErrors()) {
            throw new NotFoundHttpException('Not Found');
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
        return [
            'result' => !!$model->sendTest(),
        ];
    }
}
