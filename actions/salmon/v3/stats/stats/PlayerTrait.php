<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\stats;

use app\models\Salmon3UserStatsPlayedWith;
use app\models\User;
use yii\db\Connection;

use const SORT_ASC;
use const SORT_DESC;

trait PlayerTrait
{
    /**
     * @return Salmon3UserStatsPlayedWith[]
     */
    private function getPlayerStats(Connection $db, User $user): array
    {
        return Salmon3UserStatsPlayedWith::find()
            ->andWhere([
                'user_id' => $user->id,
            ])
            ->orderBy([
                'jobs' => SORT_DESC,
                'clear_jobs' => SORT_DESC,
                'clear_waves' => SORT_DESC,
                'max_danger_rate_cleared' => SORT_DESC,
                'max_danger_rate_played' => SORT_DESC,
                'name' => SORT_ASC,
                'number' => SORT_ASC,
            ])
            ->limit(200)
            ->all($db);
    }
}
