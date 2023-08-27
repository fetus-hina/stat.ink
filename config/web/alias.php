<?php

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
