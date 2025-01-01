<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    '@<screen_name:\w+>' => 'show-user/profile',
    '@<screen_name:\w+>/spl1/<battle:\d+>' => 'show/battle',
    '@<screen_name:\w+>/spl1/<battle:\d+>/edit' => 'show/edit-battle',
    '@<screen_name:\w+>/spl1/<id_from:\d+>-<id_to:\d+>' => 'show/user',
    '@<screen_name:\w+>/spl1/stat/report/<year:\d+>/<month:\d+>' => 'show/user-stat-report',
    '@<screen_name:\w+>/spl1/stat/report/<year:\d+>' => 'show/user-stat-report',
    '@<screen_name:\w+>/spl1/stat/<by:[\w-]+>' => 'show/user-stat-<by>',
    '@<screen_name:\w+>/spl1/' => 'show/user',
    '@<screen_name:\w+>.spl1.<lang:[\w-]+>.<type:rss|atom>' => 'feed/user',
];
