<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Map3;

final class StageApiFormatter
{
    public static function toJson(?Map3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'aliases' => AliasApiFormatter::allToJson($model->map3Aliases, $fullTranslate),
            'name' => NameApiFormatter::toJson($model->name, 'app-map3', $fullTranslate),
            'short_name' => NameApiFormatter::toJson($model->short_name, 'app-map3', $fullTranslate),
            'area' => $model->area,
            'release_at' => DateTimeApiFormatter::toJson($model->release_at),
        ];
    }
}
