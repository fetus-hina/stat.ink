<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\query;

use app\models\Weapon;
use yii\db\ActiveQuery;

use const SORT_ASC;

/**
 * @extends ActiveQuery<Weapon>
 */
final class WeaponQuery extends ActiveQuery
{
    public function naturalOrder(): self
    {
        return $this->orderBy([
            'type_id' => SORT_ASC,
            'key' => SORT_ASC,
        ]);
    }
}
