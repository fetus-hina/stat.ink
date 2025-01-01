<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\jobs;

trait JobPriority
{
    private const DEFAULT_JOB_PRIORITY = 1024;
    private const PRIORITY_LOW = +1;
    private const PRIORITY_HIGH = -1;

    public static function getJobPriority(): int
    {
        return match (static::class) {
            BattlePlayedWith3Job::class => self::lowerPriority(100),
            S3ImgGenPrefetchJob::class => self::lowerPriority(1000),
            SalmonExportJson3Job::class => self::lowerPriority(1),
            SalmonPlayedWith3Job::class => self::lowerPriority(100),
            SlackJob::class => self::higherPriority(3),
            UserExportJson3Job::class => self::lowerPriority(1),
            UserStatsJob::class => self::higherPriority(1),
            default => self::DEFAULT_JOB_PRIORITY,
        };
    }

    private static function lowerPriority(int $priority): int
    {
        return self::DEFAULT_JOB_PRIORITY + self::PRIORITY_LOW * $priority;
    }

    private static function higherPriority(int $priority): int
    {
        return self::DEFAULT_JOB_PRIORITY + self::PRIORITY_HIGH * $priority;
    }
}
