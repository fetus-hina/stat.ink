<?php
declare(strict_types=1);

use yii\swiftmailer\Mailer;

return [
    'class' => Mailer::class,
    'textLayout' => '@app/views/email/layout-text',
];
