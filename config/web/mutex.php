<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\mutex\FileMutex;

return [
    'class' => FileMutex::class,
    'mutexPath' => '@runtime/mutex',
    'dirMode' => 0700,
    'fileMode' => 0600,
];
