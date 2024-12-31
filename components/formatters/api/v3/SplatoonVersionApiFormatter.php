<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SplatoonVersion3;

final class SplatoonVersionApiFormatter
{
    public static function toJson(?SplatoonVersion3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'tag' => $model->tag,
            'name' => NameApiFormatter::toJson($model->name, 'app-version3', $fullTranslate),
            'release_at' => DateTimeApiFormatter::toJson($model->release_at),
        ];
    }
}
