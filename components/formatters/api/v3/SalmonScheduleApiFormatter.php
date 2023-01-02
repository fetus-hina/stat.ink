<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use DateTimeZone;
use app\components\helpers\DateTimeFormatter;
use app\models\SalmonSchedule3;

use function strtotime;

final class SalmonScheduleApiFormatter
{
    public static function toJson(?SalmonSchedule3 $model): ?array
    {
        if ($model === null) {
            return null;
        }

        $tz = new DateTimeZone('Etc/UTC');

        return [
            'from' => DateTimeFormatter::unixTimeToString(
                strtotime($model->start_at),
                $tz,
            ),
            'to' => DateTimeFormatter::unixTimeToString(
                strtotime($model->end_at),
                $tz,
            ),
        ];
    }
}
