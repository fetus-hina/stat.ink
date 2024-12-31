<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\web\MessagePackParser;
use app\components\web\Request;
use yii\web\JsonParser;

return [
    'class' => Request::class,
    'cookieValidationKey' => include(__DIR__ . '/../cookie-secret.php'),
    'ipHeaders' => array_merge(include(__DIR__ . '/../cloudflare/ip_headers.php'), [
        'X-Forwarded-For',
    ]),
    'secureHeaders' => array_merge(include(__DIR__ . '/../cloudflare/headers.php'), [
        'Front-End-Https',
        'X-Forwarded-For',
        'X-Forwarded-Host',
        'X-Forwarded-Proto',
        'X-Rewrite-Url',
    ]),
    'trustedHosts' => array_merge(
        file_exists(__DIR__ . '/../cloudflare/ip_ranges.php')
            ? include(__DIR__ . '/../cloudflare/ip_ranges.php')
            : [],
        [],
    ),
    'parsers' => [
        'application/json' => JsonParser::class,
        'application/msgpack' => MessagePackParser::class,
        'application/x-msgpack' => MessagePackParser::class,
    ],
];
