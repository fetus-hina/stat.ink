<?php

declare(strict_types=1);

use omnilight\scheduling\Schedule;

/**
 * @var Schedule $schedule
 */

$schedule
    ->command('stat/update-entire-salmon3')
    ->cron('47 3-23/6 * * *');
