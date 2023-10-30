<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v2\salmon;

use Yii;
use app\models\Salmon2;
use app\models\api\v2\salmon\IndexFilterForm;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\UnauthorizedHttpException;

final class IndexAction extends Action
{
    public $isAuthMode = false;

    public function init()
    {
        parent::init();

        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
    }

    public function run()
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'compact-json';

        $form = Yii::createObject([
            'class' => IndexFilterForm::class,
        ]);
        $form->attributes = Yii::$app->getRequest()->get();
        if ($this->isAuthMode) {
            if (!$user = Yii::$app->getUser()->getIdentity()) {
                throw new UnauthorizedHttpException('Unauthorized');
            }

            $form->screen_name = $user->screen_name;
        }

        if (!$query = $form->find()) {
            $resp->statusCode = 400; // bad request
            return $form->getErrors();
        }

        /**
         * @var Salmon2[] $models
         */
        $models = $query->all();

        return ArrayHelper::getColumn(
            $models,
            fn (Salmon2 $model) => $form->only === 'splatnet_number'
                ? $model->splatnet_number
                : $model->toJsonArray(),
        );
    }
}
