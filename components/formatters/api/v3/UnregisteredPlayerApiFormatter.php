<?php

/**
 * @copyright Copyright (C) 2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\UnregisteredPlayer3;

final class UnregisteredPlayerApiFormatter
{
    public static function toJson(?UnregisteredPlayer3 $model, bool $fullTranslate = false): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'name' => $model->name,
            'number' => $model->number,
            'total_battles' => $model->total_battles,
            'total_wins' => $model->total_wins,
            'win_rate' => $model->getWinRate(),
            'disconnect_rate' => $model->getDisconnectRate(),
            'performance_stats' => $model->performance_stats,
            'weapon_stats' => $model->weapon_stats,
            'lobby_stats' => $model->lobby_stats,
            'teammate_stats' => $model->teammate_stats,
        ];
    }
}
