<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\helpers;

use DateTime;
use DateTimeZone;

use function floor;
use function is_float;
use function sprintf;

class DateTimeFormatter
{
    public static function unixTimeToString(mixed $unixtime, ?DateTimeZone $tz = null): string
    {
        $isFloat = is_float($unixtime);
        $datetime = self::createDateTimeFromFloatedUnixtime((float)$unixtime);
        $datetime->setTimeZone($tz ?? self::getDefaultTimeZone());
        return $datetime->format(
            $isFloat ? 'Y-m-d\TH:i:s.uP' : 'Y-m-d\TH:i:sP',
        );
    }

    /**
     * @return array{time: int, iso8601: string}
     */
    public static function unixTimeToJsonArray(mixed $unixtime, ?DateTimeZone $tz = null): array
    {
        return [
            'time' => (int)$unixtime,
            'iso8601' => static::unixTimeToString((int)$unixtime, $tz),
        ];
    }

    private static function createDateTimeFromFloatedUnixtime(float $time): DateTime
    {
        $t1 = (int)floor((float)$time); // time の整数部
        $t2 = (float)$time - $t1; // time の小数部
        return TypeHelper::instanceOf(
            DateTime::createFromFormat(
                'U u',
                sprintf('%d %06d', $t1, (int)floor($t2 * 1000000)),
            ),
            DateTime::class,
        );
    }

    private static function getDefaultTimeZone(): DateTimeZone
    {
        //return new DateTimeZone(Yii::$app->timeZone);
        return new DateTimeZone('Etc/UTC');
    }
}
