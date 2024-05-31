<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

trait JobPriority
{
    public static function getJobPriority(): int
    {
        $defaultPriority = static::defaultPriority();
        return match (static::class) {
            SalmonExportJson3Job::class => $defaultPriority + 1,
            SlackJob::class => $defaultPriority - 3,
            UserExportJson3Job::class => $defaultPriority + 1,
            UserStatsJob::class => $defaultPriority - 1,
            default => $defaultPriority,
        };
    }

    protected static function defaultPriority(): int
    {
        return 1024;
    }
}
