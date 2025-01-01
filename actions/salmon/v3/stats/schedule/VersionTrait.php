<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\models\SalmonSchedule3;
use app\models\SplatoonVersion3;
use app\models\User;
use yii\db\Connection;

use const SORT_DESC;

trait VersionTrait
{
    private function getVersion(Connection $db, SalmonSchedule3 $schedule): ?SplatoonVersion3
    {
        return SplatoonVersion3::find()
            ->andWhere(['<=', 'release_at', $schedule->start_at])
            ->orderBy(['release_at' => SORT_DESC])
            ->limit(1)
            ->one($db);
    }
}
