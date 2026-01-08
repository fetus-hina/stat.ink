<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

// Aliases defined here and `@imageurl` are defined in a separate file (config/web/bootstrap/alias.php).
// See also there.
return [
    '@bower' => '@app/node_modules',
    '@geoip' => '@app/data/GeoIP',
    '@jdenticon' => 'https://jdenticon.stat.ink',
    '@node' => '@app/node_modules',
    '@npm' => '@app/node_modules',
    '@s3-pixel-icons' => '@npm/@hacceuee/s3-pixel-icons',
];
