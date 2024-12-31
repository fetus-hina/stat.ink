<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\User;
use yii\helpers\Url;

final class UserApiFormatter
{
    public static function toJson(
        ?User $model,
        bool $isAuthenticated = false,
        bool $fullTranslate = false,
    ): ?array {
        if (!$model) {
            return null;
        }

        return [
            'id' => $model->id,
            'name' => $model->name,
            'screen_name' => $model->screen_name,
            'url' => Url::to(
                ['show-user/profile',
                    'screen_name' => $model->screen_name,
                ],
                true,
            ),
            'icon' => Url::to($model->iconUrl, true),
        ];
    }
}
