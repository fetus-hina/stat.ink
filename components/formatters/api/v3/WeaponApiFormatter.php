<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\components\helpers\TypeHelper;
use app\models\Weapon3;
use app\models\WeaponType3;

final class WeaponApiFormatter
{
    /**
     * @return ($model is null ? null : array<string, mixed>)
     */
    public static function toJson(?Weapon3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'aliases' => AliasApiFormatter::allToJson($model->weapon3Aliases, $fullTranslate),
            'type' => WeaponTypeApiFormatter::toJson(self::getWeaponType($model), $fullTranslate),
            'name' => NameApiFormatter::toJson($model->name, 'app-weapon3', $fullTranslate),
            'matching_range' => TypeHelper::floatOrNull($model->mainweapon->matching_range),
            'main' => self::getMainWeaponKey($model),
            'sub' => SubweaponApiFormatter::toJson($model->subweapon, $fullTranslate),
            'special' => SpecialApiFormatter::toJson($model->special, $fullTranslate),
            'reskin_of' => self::getCanonicalKey($model),
        ];
    }

    // "?->" 演算子が使えないのでヘルパ関数群
    private static function getWeaponType(Weapon3 $model): WeaponType3
    {
        return $model->mainweapon->type;
    }

    private static function getMainWeaponKey(Weapon3 $model): string
    {
        return $model->mainweapon->key;
    }

    private static function getCanonicalKey(Weapon3 $model): string
    {
        return $model->canonical->key;
    }
}
