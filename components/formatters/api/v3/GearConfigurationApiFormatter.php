<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\GearConfiguration3;
use app\models\GearConfigurationSecondary3;
use yii\helpers\ArrayHelper;

use function array_map;

final class GearConfigurationApiFormatter
{
    public static function toJson(?GearConfiguration3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'primary_ability' => AbilityApiFormatter::toJson($model->ability, $fullTranslate),
            'secondary_abilities' => array_map(
                fn (GearConfigurationSecondary3 $v) => AbilityApiFormatter::toJson(
                    $v->ability,
                    $fullTranslate,
                ),
                ArrayHelper::sort(
                    $model->gearConfigurationSecondary3s,
                    fn (GearConfigurationSecondary3 $a, GearConfigurationSecondary3 $b): int => $a->id <=> $b->id,
                ),
            ),
        ];
    }
}
