<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\stats;

use app\models\Salmon3UserStatsGoldenEgg;
use app\models\Salmon3UserStatsGoldenEggIndividualHistogram;
use app\models\Salmon3UserStatsGoldenEggTeamHistogram;
use app\models\User;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

trait GoldenEggHistogramTrait
{
    /**
     * @return array<int, Salmon3UserStatsGoldenEgg>
     */
    private function getGoldenEggHistogramAbstracts(Connection $db, User $user): array
    {
        return ArrayHelper::index(
            Salmon3UserStatsGoldenEgg::find()
                ->andWhere(['user_id' => $user->id])
                ->orderBy(['map_id' => SORT_ASC])
                ->all($db),
            'map_id',
        );
    }

    /**
     * @return Salmon3UserStatsGoldenEggTeamHistogram[]
     */
    private function getGoldenEggTeamHistogram(Connection $db, User $user): array
    {
        return Salmon3UserStatsGoldenEggTeamHistogram::find()
            ->andWhere(['user_id' => $user->id])
            ->orderBy([
                'user_id' => SORT_ASC,
                'map_id' => SORT_ASC,
                'class_value' => SORT_ASC,
            ])
            ->all($db);
    }

    /**
     * @return Salmon3UserStatsGoldenEggIndividualHistogram[]
     */
    private function getGoldenEggIndividualHistogram(Connection $db, User $user): array
    {
        return Salmon3UserStatsGoldenEggIndividualHistogram::find()
            ->andWhere(['user_id' => $user->id])
            ->orderBy([
                'user_id' => SORT_ASC,
                'map_id' => SORT_ASC,
                'class_value' => SORT_ASC,
            ])
            ->all($db);
    }
}
