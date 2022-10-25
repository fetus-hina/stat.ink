<?php

declare(strict_types=1);

use app\components\helpers\UuidRegexp;

$uuid = UuidRegexp::get(false);

return [
    '@<screen_name:\w+>/spl3/stats/<subaction:[\w-]+>' => 'show-v3/stats-<subaction>',
    "@<screen_name:\w+>/spl3/<battle:{$uuid}>" => 'show-v3/battle',
    '@<screen_name:\w+>/spl3/' => 'show-v3/user',
];
