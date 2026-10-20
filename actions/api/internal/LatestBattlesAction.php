<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Yii;
use app\components\helpers\CombinedBattles;
use app\models\Battle;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Salmon2;
use app\models\Salmon3;
use yii\db\Transaction;

class LatestBattlesAction extends BaseLatestBattlesAction
{
    private const BATTLE_LIMIT = 100;

    /**
     * @return array<Battle|Battle2|Battle3|Salmon2|Salmon3>
     */
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
