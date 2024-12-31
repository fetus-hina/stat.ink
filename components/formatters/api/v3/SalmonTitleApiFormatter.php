<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SalmonTitle3;

final class SalmonTitleApiFormatter
{
    public static function toJson(?SalmonTitle3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'aliases' => AliasApiFormatter::allToJson($model->salmonTitle3Aliases, $fullTranslate),
            'name' => NameApiFormatter::toJson($model->name, 'app-salmon-title3', $fullTranslate),
        ];
    }
}
