<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Medal3;

use function array_map;

final class MedalApiFormatter
{
    /**
     * @param Medal3[]|null $models
     */
    public static function toJson(?array $models, bool $fullTranslate = false): ?array
    {
        if (!$models) {
            return null;
        }

        return array_map(
            fn (Medal3 $model): string => $model->name,
            $models,
        );
    }
}
