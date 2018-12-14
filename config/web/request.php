<?php
declare(strict_types=1);

use app\components\web\MessagePackParser;
use app\components\web\Request;
use yii\web\JsonParser;

return [
    'class' => Request::class,
    'cookieValidationKey' => include(__DIR__ . '/../cookie-secret.php'),
    'parsers' => [
        'application/json' => JsonParser::class,
        'application/x-msgpack' => MessagePackParser::class,
    ],
];
