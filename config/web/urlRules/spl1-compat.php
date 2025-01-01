<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    'u/<screen_name:\w+>/<battle:\d+>' => 'show-compat/battle',
    'u/<screen_name:\w+>/<battle:\d+>/edit' => 'show-compat/edit-battle',
    'u/<screen_name:\w+>/<id_from:\d+>-<id_to:\d+>' => 'show-compat/user-fromto',
    'u/<screen_name:\w+>/stat/report/<year:\d+>/<month:\d+>' => 'show-compat/user-stat-report-ym',
    'u/<screen_name:\w+>/stat/report/<year:\d+>' => 'show-compat/user-stat-report-y',
    'u/<screen_name:\w+>' => 'show-compat/user',
    'u/<screen_name:\w+>.<lang:[\w-]+>.<type:rss|atom>' => 'feed/compat-user',
];
