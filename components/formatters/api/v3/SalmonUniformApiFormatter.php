<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SalmonUniform3;

final class SalmonUniformApiFormatter
{
    public static function toJson(?SalmonUniform3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'aliases' => AliasApiFormatter::allToJson($model->salmonUniform3Aliases, $fullTranslate),
            'name' => NameApiFormatter::toJson($model->name, 'app-salmon-uniform3', $fullTranslate),
        ];
    }
}
