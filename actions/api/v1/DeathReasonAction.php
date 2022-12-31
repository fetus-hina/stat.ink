<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v1;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\DeathReason;
use app\models\api\v1\DeathReasonGetForm;

class DeathReasonAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = new DeathReasonGetForm();
        $form->attributes = Yii::$app->getRequest()->get();
        if (!$form->validate()) {
            $response->statusCode = 400;
            return [
                'error' => $form->getErrors(),
            ];
        }

        $query = DeathReason::find()
            ->with(['type'])
            ->orderBy('[[id]]');
        $form->filterQuery($query);

        return array_map(
            function ($model) {
                return $model->toJsonArray();
            },
            $query->all(),
        );
    }
}
