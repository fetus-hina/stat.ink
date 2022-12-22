<?php

declare(strict_types=1);

use omnilight\scheduling\Schedule;

/**
 * @var Schedule $schedule
 */

$schedule
    ->command('user-json/auto-update')
    ->cron('* * * * *');
