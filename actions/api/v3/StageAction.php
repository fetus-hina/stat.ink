<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Translator;
use app\models\Map3;
use app\models\Map3Alias;
use yii\web\ViewAction;

final class StageAction extends ViewAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        return array_map(
            function (Map3 $model): array {
                // FIXME: toJsonArray
                // return $model->toJsonArray();
                $t = $model->release_at ? \strtotime($model->release_at) : null;
                return [
                    'key' => $model->key,
                    'aliases' => self::sortArray(
                        array_map(
                            function (Map3Alias $model): string {
                                return $model->key;
                            },
                            $model->map3Aliases,
                        ),
                    ),
                    // 'splatnet' => $model->splatnet,
                    'name' => Translator::translateToAll('app-map3', $model->name, [], 3),
                    'short_name' => Translator::translateToAll(
                        'app-map3',
                        $model->short_name,
                        [],
                        3,
                    ),
                    'area' => $model->area,
                    'release_at' => $t
                        ? DateTimeFormatter::unixTimeToJsonArray($t, new DateTimeZone('Etc/UTC'))
                        : null,
                ];
            },
            Map3::find()->orderBy(['id' => SORT_ASC])->all()
        );
    }

    private static function sortArray(array $values): array
    {
        sort($values, SORT_STRING);
        return $values;
    }
}
