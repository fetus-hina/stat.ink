<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v2\salmon;

use Yii;
use app\models\Salmon2;
use yii\web\NotFoundHttpException;

use function filter_var;

use const FILTER_VALIDATE_INT;

class ViewAction extends \yii\web\ViewAction
{
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

        $id = filter_var(Yii::$app->getRequest()->get('id'), FILTER_VALIDATE_INT);
        if ($id === false) {
            throw new NotFoundHttpException('Not found');
        }

        if (!$model = Salmon2::findOne(['id' => $id])) {
            throw new NotFoundHttpException('Not found');
        }

        return $model->toJsonArray();
    }
}
