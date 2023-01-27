<?php

declare(strict_types=1);

use app\components\helpers\UuidRegexp;

$uuid = UuidRegexp::get(false);

return [
    "DELETE @<screen_name:\w+>/salmon3/<battle:{$uuid}>" => 'salmon-v3/delete',
    "DELETE @<screen_name:\w+>/spl3/<battle:{$uuid}>" => 'show-v3/delete-battle',

    '@<screen_name:\w+>/spl3/index.json' => 'show-v3/user-json',
    '@<screen_name:\w+>/spl3/stats/<subaction:[\w-]+>' => 'show-v3/stats-<subaction>',
    "@<screen_name:\w+>/spl3/<battle:{$uuid}>" => 'show-v3/battle',
    '@<screen_name:\w+>/spl3/' => 'show-v3/user',

    '@<screen_name:\w+>/salmon3/' => 'salmon-v3/index',
    "@<screen_name:\w+>/salmon3/<battle:{$uuid}>" => 'salmon-v3/view',
];
