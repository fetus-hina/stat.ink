<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\TricolorRole3;

final class TricolorRoleApiFormatter
{
    public static function toJson(?TricolorRole3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'name' => NameApiFormatter::toJson($model->name, 'app-rule3', $fullTranslate),
        ];
    }
}
