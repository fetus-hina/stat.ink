<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
