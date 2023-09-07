<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SalmonPlayer3;
use app\models\SalmonPlayerWeapon3;

use function array_map;
use function array_values;

final class SalmonPlayerApiFormatter
{
    /**
     * @param SalmonPlayer3[] $models
     */
    public static function allToJson(array $models, bool $fullTranslate = false): ?array
    {
        return array_map(
            fn (?SalmonPlayer3 $model): ?array => self::toJson($model, $fullTranslate),
            array_values($models),
        );
    }

    public static function toJson(?SalmonPlayer3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'me' => $model->is_me,
            'name' => $model->name,
            'number' => $model->number,
            'splashtag_title' => SplashtagTitleApiFormatter::toJson($model->splashtagTitle),
            'uniform' => SalmonUniformApiFormatter::toJson($model->uniform, $fullTranslate),
            'special' => SpecialApiFormatter::toJson($model->special, $fullTranslate),
            'weapons' => array_map(
                fn (?SalmonPlayerWeapon3 $model): ?array => SalmonWeaponApiFormatter::toJson(
                    $model->weapon,
                    $fullTranslate,
                ),
                array_values($model->salmonPlayerWeapon3s),
            ),
            'golden_eggs' => $model->golden_eggs,
            'golden_assist' => $model->golden_assist,
            'power_eggs' => $model->power_eggs,
            'rescue' => $model->rescue,
            'rescued' => $model->rescued,
            'defeat_boss' => $model->defeat_boss,
            'species' => SpeciesApiFormatter::toJson($model->species, $fullTranslate),
            'disconnected' => $model->is_disconnected,
        ];
    }
}
