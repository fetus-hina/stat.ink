<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SalmonWave3;

use function array_map;
use function array_values;

final class SalmonWaveApiFormatter
{
    /**
     * @param SalmonWave3[] $models
     */
    public static function allToJson(array $models, bool $fullTranslate = false): ?array
    {
        return array_map(
            fn (?SalmonWave3 $model): ?array => self::toJson($model, $fullTranslate),
            array_values($models),
        );
    }

    public static function toJson(?SalmonWave3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'tide' => SalmonTideApiFormatter::toJson($model->tide, $fullTranslate),
            'event' => SalmonEventApiFormatter::toJson($model->event, $fullTranslate),
            'golden_quota' => $model->golden_quota,
            'golden_delivered' => $model->golden_delivered,
            'golden_appearances' => $model->golden_appearances,
            'special_uses' => SalmonSpecialUseApiFormatter::allToJson(
                $model->salmonSpecialUse3s,
                $fullTranslate,
            ),
        ];
    }
}
