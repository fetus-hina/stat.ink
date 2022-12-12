<?php

declare(strict_types=1);

use omnilight\scheduling\Schedule;

/**
 * @var Schedule $schedule
 */

$schedule
    ->command('geoip/update')
    ->cron('31 3 * * 4');
