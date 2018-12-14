<?php
declare(strict_types=1);

return [
    'timeout' => 86400,
    'cookieParams' => [
        'httponly' => true,
        'secure' => (bool)preg_match(
            '/(?:^|\.)stat\.ink$/i',
            $_SERVER['HTTP_HOST'] ?? ''
        ),
    ],
];
