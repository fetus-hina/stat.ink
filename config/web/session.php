<?php

declare(strict_types=1);

use app\components\web\Session;

return [
    'class' => Session::class,
    'timeout' => 86400,
    'cacheLimiter' => 'nocache',
    'cookieParams' => [
        'httponly' => true,
        'secure' => (bool)preg_match(
            '/(?:^|\.)stat\.ink$/i',
            $_SERVER['HTTP_HOST'] ?? ''
        ),
    ],
];
