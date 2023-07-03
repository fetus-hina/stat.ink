<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\stats;

use app\models\Salmon3UserStatsSpecial;
use app\models\Special3;
use app\models\User;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

trait SpecialTrait
{
    /**
     * @return array<int, Salmon3UserStatsSpecial>
     */
    private function getSpecialStats(Connection $db, User $user): array
    {
        return ArrayHelper::index(
            Salmon3UserStatsSpecial::find()
                ->andWhere(['user_id' => $user->id])
                ->all($db),
            'special_id',
        );
    }

    /**
     * @return array<int, Special3>
     */
    private function getSpecials(Connection $db): array
    {
        return ArrayHelper::index(
            Special3::find()->orderBy(['rank' => SORT_ASC])->all(),
            'id',
        );
    }
}
