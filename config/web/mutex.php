<?php

declare(strict_types=1);

use yii\mutex\FileMutex;

return [
    'class' => FileMutex::class,
    'mutexPath' => '@runtime/mutex',
    'dirMode' => 0700,
    'fileMode' => 0600,
];
