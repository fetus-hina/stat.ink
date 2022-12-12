<?php

declare(strict_types=1);

use omnilight\scheduling\Schedule;

/**
 * @var Schedule $schedule
 */

$schedule
    ->command('splatoon2-ink/update')
    ->cron('4 * * * *');

$schedule
    ->command('splatoon3-ink/update')
    ->cron('2 * * * *');
