<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use DateTime;
use DateTimeZone;
use Yii;

class DateTimeFormatter
{
    public static function unixTimeToString($unixtime, DateTimeZone $tz = null)
    {
        $isFloat = is_float($unixtime);
        $datetime = self::createDateTimeFromFloatedUnixtime((float)$unixtime);
        $datetime->setTimeZone($tz === null ? self::getDefaultTimeZone() : $tz);
        return $datetime->format(
            $isFloat ? 'Y-m-d\TH:i:s.uP' : 'Y-m-d\TH:i:sP'
        );
    }

    private static function createDateTimeFromFloatedUnixtime($time)
    {
        $t1 = (int)floor((float)$time); // time の整数部
        $t2 = (float)$time - $t1;       // time の小数部
        return DateTime::createFromFormat(
            'U u',
            sprintf('%d %06d', $t1, (int)floor($t2 * 1000000))
        );
    }

    private static function getDefaultTimeZone()
    {
        return new DateTimeZone(Yii::$app->timeZone);
    }
}
