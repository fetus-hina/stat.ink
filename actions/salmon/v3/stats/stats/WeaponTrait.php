<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\stats;

use app\models\Salmon3UserStatsWeapon;
use app\models\SalmonWeapon3;
use app\models\User;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

use const SORT_ASC;
use const SORT_DESC;

trait WeaponTrait
{
    /**
     * @return array<int, Salmon3UserStatsWeapon>
     */
    private function getWeaponStats(Connection $db, User $user): array
    {
        return ArrayHelper::index(
            Salmon3UserStatsWeapon::find()
                ->andWhere(['user_id' => $user->id])
                ->orderBy([
                    '[[total_waves]]' => SORT_DESC,
                    '[[normal_waves]]' => SORT_DESC,
                    '[[normal_waves_cleared]]' => SORT_DESC,
                    '[[xtra_waves_cleared]]' => SORT_DESC,
                    '[[weapon_id]]' => SORT_ASC,
                ])
                ->all($db),
            'weapon_id',
        );
    }

    /**
     * @return array<int, SalmonWeapon3>
     */
    private function getWeapons(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonWeapon3::find()->orderBy(['id' => SORT_ASC])->all(),
            'id',
        );
    }
}
