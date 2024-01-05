<?php

declare(strict_types=1);

use app\components\web\Session;
use yii\web\Cookie;

return [
    'class' => Session::class,
    'cacheLimiter' => 'nocache',
    'cookieParams' => [
        'httponly' => true,
        'sameSite' => Cookie::SAME_SITE_LAX,
        'secure' => (bool)preg_match(
            '/(?:^|\.)stat\.ink$/i',
            $_SERVER['HTTP_HOST'] ?? '',
        ),
    ],
    'db' => 'db',
    'name' => YII_ENV_DEV ? 'SESSID_DEVENV' : 'PHPSESSID',
    'sessionTable' => '{{%session}}',
    'timeout' => 86400,
];
