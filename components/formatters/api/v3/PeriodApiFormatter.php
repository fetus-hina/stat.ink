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

final class PeriodApiFormatter
{
    /**
     * @param int|null $value
     */
    public static function toJson($value): ?array
    {
        if (!\is_int($value)) {
            return null;
        }

        $t = $value * 7200;
        $tz = new DateTimeZone('Etc/UTC');

        return [
            'period' => $value,
            'from' => DateTimeFormatter::unixTimeToString($t, $tz),
            'to' => DateTimeFormatter::unixTimeToString($t + 7200, $tz),
        ];
    }
}
