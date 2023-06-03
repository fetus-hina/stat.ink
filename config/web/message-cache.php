<?php

declare(strict_types=1);

use yii\caching\FileCache;

return [
    'class' => FileCache::class,
    'cachePath' => '@runtime/message-cache',
];
