<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Weapon3;
use app\models\WeaponType3;

final class WeaponApiFormatter
{
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
            'main' => self::getMainWeaponKey($model),
            'sub' => SubweaponApiFormatter::toJson($model->subweapon, $fullTranslate),
            'special' => SpecialApiFormatter::toJson($model->special, $fullTranslate),
            'reskin_of' => self::getCanonicalKey($model),
        ];
    }

    // "?->" 演算子が使えないのでヘルパ関数群
    private static function getWeaponType(Weapon3 $model): ?WeaponType3
    {
        $main = $model->mainweapon;
        return $main ? $main->type : null;
    }

    private static function getMainWeaponKey(Weapon3 $model): ?string
    {
        $main = $model->mainweapon;
        return $main ? $main->key : null;
    }

    private static function getCanonicalKey(Weapon3 $model): ?string
    {
        $model2 = $model->canonical;
        return $model2 ? $model2->key : null;
    }
}
