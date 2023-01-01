<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\components\ImageS3;

$japanTime = 9 * 3600;
$data = [
    'class' => ImageS3::class,
    'enabled' => false,
    'endpoint' => 's3-ap-northeast-1.amazonaws.com',
    'accessKey' => '',
    'secret' => '',
    'bucket' => '',
];

$lines = [
    '<?php',
    '',
    '/**',
    ' * @copyright Copyright (C) 2015-' . gmdate('Y', time() + $japanTime) . ' AIZAWA Hina',
    ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT',
    ' * @author AIZAWA Hina <hina@fetus.jp>',
    ' */',
    '',
    'declare(strict_types=1);',
    '',
    'return [',
];
foreach ($data as $key => $value) {
    $lines[] = vsprintf('    %s => %s,', [
        "'" . addslashes($key) . "'",
        is_bool($value)
            ? ($value ? 'true' : 'false')
            : ("'" . addslashes($value) . "'"),
    ]);
}
$lines[] = '];';

echo implode("\n", $lines) . "\n";
