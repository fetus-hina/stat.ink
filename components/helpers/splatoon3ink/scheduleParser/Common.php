<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\splatoon3ink\scheduleParser;

use DateTimeImmutable;
use DateTimeZone;

trait Common
{
    private static function parseTimestamp(string $ts): int
    {
        return (new DateTimeImmutable($ts, new DateTimeZone('Etc/UTC')))
            ->getTimestamp();
    }
}
