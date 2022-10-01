<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Rule3;

final class RuleApiFormatter
{
    public static function toJson(?Rule3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'aliases' => AliasApiFormatter::allToJson($model->rule3Aliases, $fullTranslate),
            'name' => NameApiFormatter::toJson($model->name, 'app-rule3', $fullTranslate),
            'short_name' => NameApiFormatter::toJson($model->short_name, 'app-rule3', $fullTranslate),
        ];
    }
}
