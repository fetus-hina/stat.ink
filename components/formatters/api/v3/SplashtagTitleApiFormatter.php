<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SplashtagTitle3;

final class SplashtagTitleApiFormatter
{
    public static function toJson(?SplashtagTitle3 $model): ?string
    {
        if (!$model) {
            return null;
        }

        return $model->name;
    }
}
