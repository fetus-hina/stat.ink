<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
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

        switch (static::class) {
            case SlackJob::class:
                return $defaultPriority - 3;

            case OstatusJob::class:
                return $defaultPriority - 2;

            case UserStatsJob::class:
                return $defaultPriority - 1;

            case ImageS3Job::class:
                return $defaultPriority + 1;

            default:
                return $defaultPriority;
        }
    }

    protected static function defaultPriority(): int
    {
        return 1024;
    }
}
