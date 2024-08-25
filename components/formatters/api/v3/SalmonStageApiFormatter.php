<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\BigrunMap3;
use app\models\Map3;
use app\models\SalmonMap3;

final class SalmonStageApiFormatter
{
    public static function toJson(
        ?SalmonMap3 $coopStage,
        Map3|BigrunMap3|null $vsStage,
        bool $fullTranslate = false,
    ): ?array {
        if (!$coopStage && !$vsStage) {
            return null;
        }

        return match (true) {
            $coopStage !== null => [
                'key' => $coopStage->key,
                'aliases' => AliasApiFormatter::allToJson(
                    $coopStage->salmonMap3Aliases,
                    $fullTranslate,
                ),
                'name' => NameApiFormatter::toJson($coopStage->name, 'app-map3', $fullTranslate),
            ],
            $vsStage instanceOf BigrunMap3 => [
                'key' => $vsStage->key,
                'aliases' => AliasApiFormatter::allToJson(
                    $vsStage->bigrunMap3Aliases,
                    $fullTranslate,
                ),
                'name' => NameApiFormatter::toJson($vsStage->name, 'app-map3', $fullTranslate),
            ],
            $vsStage instanceOf Map3 => StageApiFormatter::toJson($vsStage, $fullTranslate),
            default => null,
        };
    }
}
