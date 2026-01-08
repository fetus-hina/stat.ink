<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\web\UrlNormalizer;

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => true,
    'normalizer' => [
        'class' => UrlNormalizer::class,
        'normalizeTrailingSlash' => false,
    ],
    'rules' => array_merge(
        require __DIR__ . '/urlRules/user.php',
        require __DIR__ . '/urlRules/spl1-compat.php',
        require __DIR__ . '/urlRules/spl1.php',
        require __DIR__ . '/urlRules/spl2.php',
        require __DIR__ . '/urlRules/spl3.php',
        require __DIR__ . '/urlRules/stats.php',
        require __DIR__ . '/urlRules/api1.php',
        require __DIR__ . '/urlRules/api2.php',
        require __DIR__ . '/urlRules/api3.php',
        require __DIR__ . '/urlRules/api-internal.php',
        require __DIR__ . '/urlRules/meta.php',
    ),
];
