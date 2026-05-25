<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

final class YearMonth
{
    public static function fromDateString(string $dateString): int
    {
        return self::fromDateTime(new DateTimeImmutable($dateString));
    }

    public static function fromDateTime(DateTimeInterface $dt): int
    {
        return (int)(new DateTimeImmutable('@' . $dt->getTimestamp()))
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->format('Ym');
    }

    private function __construct()
    {
    }
}
