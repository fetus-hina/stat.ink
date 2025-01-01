<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    '<action:[\w-]+>' => 'site/<action>',
    '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    'robots.txt' => 'site/robots',
    '' => 'site/index',
];
