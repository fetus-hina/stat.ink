<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Splatfest3Theme;

final class SplatfestThemeApiFormatter
{
    public static function toJson(?Splatfest3Theme $model, bool $fullTranslate = false): ?string
    {
        if (!$model) {
            return null;
        }

        return $model->name;
    }
}
