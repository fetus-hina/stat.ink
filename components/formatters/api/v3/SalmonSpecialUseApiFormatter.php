<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SalmonSpecialUse3;
use yii\helpers\ArrayHelper;

use function array_values;

final class SalmonSpecialUseApiFormatter
{
    /**
     * @param SalmonSpecialUse3[] $models
     */
    public static function allToJson(array $models, bool $fullTranslate = false): ?array
    {
        return ArrayHelper::map(
            array_values($models),
            'special.key',
            fn (SalmonSpecialUse3 $model): array => self::toJson($model, $fullTranslate),
        );
    }

    public static function toJson(?SalmonSpecialUse3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'special' => SpecialApiFormatter::toJson($model->special, $fullTranslate),
            'count' => $model->count,
        ];
    }
}
