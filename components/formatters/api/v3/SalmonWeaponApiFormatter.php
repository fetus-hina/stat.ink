<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SalmonWeapon3;

final class SalmonWeaponApiFormatter
{
    public static function toJson(?SalmonWeapon3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'aliases' => AliasApiFormatter::allToJson($model->salmonWeapon3Aliases, $fullTranslate),
            'name' => NameApiFormatter::toJson($model->name, 'app-weapon3', $fullTranslate),
        ];
    }
}
