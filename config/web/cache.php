<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\caching\DummyCache;
use yii\caching\FileCache;

$enabled = false;

return $enabled || YII_ENV_PROD
    ? [
        'class' => FileCache::class,

        'cachePath' => '@runtime/cache-v2',
        'defaultDuration' => 3600,
        'dirMode' => 0755,
        'fileMode' => 0644,
        'gcProbability' => 0,
    ]
    : [
        'class' => DummyCache::class,
    ];
