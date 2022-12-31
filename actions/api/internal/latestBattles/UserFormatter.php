<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\latestBattles;

use app\models\User;
use yii\helpers\Url;

trait UserFormatter
{
    protected function formatUser(User $model): array
    {
        return [
            'icon' => \array_values(
                \array_filter([
                    $model->userIcon
                        ? Url::to($model->userIcon->url, true)
                        : null,
                    Url::to($model->jdenticonUrl, true),
                ]),
            ),
            'name' => $model->name,
            'url' => Url::to(
                ['show-user/profile',
                    'screen_name' => $model->screen_name,
                ],
                true,
            ),
        ];
    }
}
