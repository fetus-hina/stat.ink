<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SalmonBoss3;
use app\models\SalmonBossAppearance3;
use yii\helpers\ArrayHelper;

use function ksort;

final class SalmonBossAppearanceApiFormatter
{
    /**
     * @param SalmonBossAppearance3[] $models
     */
    public static function allToJson(array $models, bool $fullTranslate = false): array
    {
        static $bosses = null;
        if ($bosses === null) {
            $bosses = ArrayHelper::map(
                SalmonBoss3::find()->with('salmonBoss3Aliases')->all(),
                'id',
                fn (SalmonBoss3 $model): SalmonBoss3 => $model,
            );
        }

        $results = [];
        foreach ($models as $model) {
            $boss = $bosses[$model->boss_id];
            $results[$boss->key] = [
                'boss' => SalmonBossApiFormatter::toJson($boss, $fullTranslate),
                'appearances' => (int)$model->appearances,
                'defeated' => (int)$model->defeated,
                'defeated_by_me' => (int)$model->defeated_by_me,
            ];
        }
        ksort($results);
        return $results;
    }
}
