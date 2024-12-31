<?php

/**
 * @copyright Copyright (C) 2020-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Yii;
use app\components\helpers\CombinedBattles;
use yii\db\Transaction;

class LatestBattlesAction extends BaseLatestBattlesAction
{
    private const BATTLE_LIMIT = 100;

    protected function fetchBattles(): array
    {
        return Yii::$app->db->transaction(
            fn (): array => CombinedBattles::getRecentBattles(
                self::BATTLE_LIMIT,
            ),
            Transaction::READ_COMMITTED,
        );
    }

    protected function getHeading(): string
    {
        return Yii::t('app', 'Recent Battles');
    }
}
