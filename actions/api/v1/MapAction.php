<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v1;

use Yii;
use app\models\Map;
use yii\web\ViewAction as BaseAction;

use function array_map;

class MapAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        return array_map(
            fn ($map) => $map->toJsonArray(),
            Map::find()->orderBy('{{map}}.[[id]] ASC')->all(),
        );
    }
}
