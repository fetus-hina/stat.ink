<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v2;

use Yii;
use app\models\Map2;
use yii\web\ViewAction as BaseAction;

use const SORT_ASC;

class StageAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        return array_map(
            fn (Map2 $map): array => $map->toJsonArray(),
            Map2::find()->orderBy(['id' => SORT_ASC])->all()
        );
    }
}
