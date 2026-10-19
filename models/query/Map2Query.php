<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\query;

use app\models\Map2;
use yii\db\ActiveQuery;

/**
 * @extends ActiveQuery<Map2>
 */
final class Map2Query extends ActiveQuery
{
    public function excludeMystery(): self
    {
        return $this->andWhere(['and',
            ['not', ['like', 'key', 'mystery%', false]],
        ]);
    }
}
