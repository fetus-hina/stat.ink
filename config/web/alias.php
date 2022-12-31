<?php

declare(strict_types=1);

return (function (): array {
    $p = require dirname(__DIR__) . '/params.php';

    return [
        '@bower' => '@app/node_modules',
        '@geoip' => '@app/data/GeoIP',
        '@imageurl' => ($p['useImgStatInk'] ?? false) ? 'https://img.stat.ink' : '@web/images',
        '@jdenticon' => 'https://jdenticon.stat.ink',
        '@node' => '@app/node_modules',
        '@npm' => '@app/node_modules',
    ];
})();
