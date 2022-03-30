<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use app\models\StatWeapon2Tier;
use yii\db\ActiveQuery;

final class StatWeapon2TierQuery extends ActiveQuery
{
    public function thresholded(): self
    {
        $this->andWhere(
            ['>=',
                '{{stat_weapon2_tier}}.[[players_count]]',
                StatWeapon2Tier::PLAYERS_COUNT_THRESHOLD,
            ]
        );
        return $this;
    }
}
