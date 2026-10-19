<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\query;

use app\models\UserWeapon2;
use yii\db\ActiveQuery;

use const SORT_DESC;

/**
 * @extends ActiveQuery<UserWeapon2>
 */
final class UserWeapon2Query extends ActiveQuery
{
    public function favoriteOrder(): self
    {
        return $this
            ->andWhere(['>', 'battles', 0])
            ->orderBy(['battles' => SORT_DESC]);
    }
}
