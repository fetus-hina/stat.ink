<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Subweapon3;

final class SubweaponApiFormatter
{
    public static function toJson(?Subweapon3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'key' => $model->key,
            'aliases' => AliasApiFormatter::allToJson([], $fullTranslate),
            'name' => NameApiFormatter::toJson($model->name, 'app-subweapon3', $fullTranslate),
        ];
    }
}
