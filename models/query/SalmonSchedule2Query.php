<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use app\components\helpers\db\Now;
use yii\db\ActiveQuery;

use const SORT_ASC;
use const SORT_DESC;

final class SalmonSchedule2Query extends ActiveQuery
{
    /** @inheritdoc */
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
