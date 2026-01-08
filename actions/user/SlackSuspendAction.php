<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use app\components\helpers\db\Now;
use app\models\Slack;
use yii\base\DynamicModel;
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
        $form = DynamicModel::validateData(
            [
                'id' => $req->post('id'),
                'suspend' => $req->post('suspend'),
            ],
            [
                [['id'], 'exist', 'targetClass' => Slack::class, 'targetAttribute' => 'id'],
                [['suspend'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            ],
        );
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
