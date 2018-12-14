<?php
declare(strict_types=1);

return (function (): array {
    $p = require(dirname(__DIR__) . '/params.php');

    return [
        '@bower' => '@vendor/bower-asset',
        '@imageurl' => ($p['useImgStatInk'] ?? false) ? 'https://img.stat.ink' : '@web/images',
        '@jdenticon' => 'https://jdenticon.stat.ink',
        '@npm'   => '@vendor/npm-asset',
    ];
})();
