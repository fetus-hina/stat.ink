<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\query;

use app\components\helpers\db\Now;
use app\models\SalmonSchedule2;
use yii\db\ActiveQuery;

use const SORT_ASC;
use const SORT_DESC;

/**
 * @extends ActiveQuery<SalmonSchedule2>
 */
final class SalmonSchedule2Query extends ActiveQuery
{
    public function init()
    {
        parent::init();
        $this->olderFirst();
    }

    public function nowOrFuture(): self
    {
        return $this->andWhere(['and',
            ['>=', '{{salmon_schedule2}}.[[end_at]]', new Now()],
        ]);
    }

    public function nowOrPast(): self
    {
        return $this->andWhere(['and',
            ['<=', '{{salmon_schedule2}}.[[start_at]]', new Now()],
        ]);
    }

    public function olderFirst(): self
    {
        return $this->orderBy([
            '{{salmon_schedule2}}.[[start_at]]' => SORT_ASC,
        ]);
    }

    public function newerFirst(): self
    {
        return $this->orderBy([
            '{{salmon_schedule2}}.[[start_at]]' => SORT_DESC,
        ]);
    }
}
