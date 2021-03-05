<?php

declare(strict_types=1);

use yii\mutex\PgsqlMutex;

return [
    'class' => PgsqlMutex::class,
    'db' => 'db',
];
