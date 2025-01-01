<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\dlStats3;

use DateTimeImmutable;
use DateTimeZone;

use function time;

trait DateUtilTrait
{
    private static function startDay(): DateTimeImmutable
    {
        return (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone(self::TIMEZONE))
            ->setDate(2022, 9, 9)
            ->setTime(0, 0, 0);
    }

    private static function today(): DateTimeImmutable
    {
        return (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone(self::TIMEZONE))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
            ->setTime(0, 0, 0);
    }
}
