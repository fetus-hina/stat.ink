<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\query;

use app\components\helpers\db\Now;
use app\models\MaintenanceSchedule;
use yii\db\ActiveQuery;

use const SORT_ASC;

/**
 * @extends ActiveQuery<MaintenanceSchedule>
 */
final class MaintenanceScheduleQuery extends ActiveQuery
{
    public function enabled(): self
    {
        $table = MaintenanceSchedule::tableName();
        $this->andWhere(['and',
            ["{{{$table}}}.[[enabled]]" => true],
            ['>', "{{{$table}}}.[[end_at]]", new Now()],
        ]);
        return $this;
    }

    public function recently(): self
    {
        $table = MaintenanceSchedule::tableName();
        $this->orderBy([
            "{{{$table}}}.[[start_at]]" => SORT_ASC,
        ]);
        return $this;
    }
}
