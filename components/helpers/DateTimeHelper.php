<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Yii;
use app\components\helpers\dateTimeHelper\FormatTrait;

final class DateTimeHelper
{
    use FormatTrait;

    public static function now(): DateTimeImmutable
    {
        return (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($_SERVER['REQUEST_TIME']);
    }

    public static function utcNow(): DateTimeImmutable
    {
        return self::now()->setTimezone(new DateTimeZone('Etc/UTC'));
    }

    public static function isoNow(): string
    {
        return self::isoString(self::now());
    }

    public static function isoUtcNow(): string
    {
        return self::isoString(self::utcNow());
    }

    private static function isoString(DateTimeInterface $ts): string
    {
        return $ts->format(DateTimeInterface::ATOM);
    }
}
