<?php

declare(strict_types=1);

use yii\symfonymailer\Mailer;

return [
    'class' => Mailer::class,
    'textLayout' => '@app/views/email/layout-text',
    'transport' => [
        'scheme' => 'smtp',
        'host' => 'localhost',
        'port' => 25,
        'dsn' => 'native://default',
    ],
];
