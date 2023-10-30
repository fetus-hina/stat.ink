<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Map3;
use app\models\SalmonMap3;

final class SalmonStageApiFormatter
{
    public static function toJson(
        ?SalmonMap3 $coopStage,
        ?Map3 $vsStage,
        bool $fullTranslate = false,
    ): ?array {
        if (!$coopStage && !$vsStage) {
            return null;
        }

        return $coopStage
            ? [
                'key' => $coopStage->key,
                'aliases' => AliasApiFormatter::allToJson(
                    $coopStage->salmonMap3Aliases,
                    $fullTranslate,
                ),
                'name' => NameApiFormatter::toJson($coopStage->name, 'app-map3', $fullTranslate),
            ]
            : StageApiFormatter::toJson($vsStage, $fullTranslate);
    }
}
