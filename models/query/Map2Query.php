<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\query;

use yii\db\ActiveQuery;

final class Map2Query extends ActiveQuery
{
    public function excludeMystery(): self
    {
        return $this->andWhere(['and',
            ['not', ['like', 'key', 'mystery%', false]],
        ]);
    }
}
