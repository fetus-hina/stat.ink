<?php

declare(strict_types=1);

use yii\caching\FileCache;

return [
    'class' => FileCache::class,

    'cachePath' => '@runtime/cache-v2',
    'defaultDuration' => 3600,
    'dirMode' => 0755,
    'fileMode' => 0644,
    'gcProbability' => 0,
];
