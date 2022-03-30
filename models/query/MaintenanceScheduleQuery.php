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

final class MaintenanceScheduleQuery extends ActiveQuery
{
    public function enabled(): self
    {
        $this->andWhere(['and',
            ['{{maintenance_schedule}}.[[enabled]]' => true],
            ['>', '{{maintenance_schedule}}.[[end_at]]', new Now()],
        ]);

        return $this;
    }

    public function recently(): self
    {
        $this->orderBy([
            '{{maintenance_schedule}}.[[start_at]]' => SORT_ASC,
        ]);

        return $this;
    }
}
