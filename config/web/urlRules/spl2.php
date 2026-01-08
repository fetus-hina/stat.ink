<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    '@<screen_name:\w+>/spl2/<battle:\d+>' => 'show-v2/battle',
    '@<screen_name:\w+>/spl2/<battle:\d+>/edit' => 'show-v2/edit-battle',
    '@<screen_name:\w+>/spl2/<id_from:\d+>-<id_to:\d+>' => 'show-v2/user',
    '@<screen_name:\w+>/spl2/stat/monthly-report/<year:\d+>/<month:\d+>' => 'show-v2/user-stat-monthly-report',
    '@<screen_name:\w+>/spl2/stat/report/<year:\d+>/<month:\d+>' => 'show-v2/user-stat-report',
    '@<screen_name:\w+>/spl2/stat/report/<year:\d+>' => 'show-v2/user-stat-report',
    '@<screen_name:\w+>/spl2/stat/<by:[\w-]+>' => 'show-v2/user-stat-<by>',
    '@<screen_name:\w+>/spl2/' => 'show-v2/user',
    '@<screen_name:\w+>.2.<lang:[\w-]+>.<type:rss|atom>' => 'feed/user-v2',

    '@<screen_name:\w+>/salmon' => 'salmon/index',
    '@<screen_name:\w+>/salmon/<id:\d+>' => 'salmon/view',
    '@<screen_name:\w+>/salmon/<id:\d+>/edit' => 'salmon/edit',
    '@<screen_name:\w+>/salmon/<id:\d+>/delete' => 'salmon/delete',
    '@<screen_name:\w+>/salmon/index.<lang:[\w-]+>.<type:rss|atom>' => 'salmon/feed',
];
