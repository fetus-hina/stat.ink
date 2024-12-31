<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\mutex\PgsqlMutex;
use yii\queue\db\Queue;

return [
    'class' => Queue::class,
    'db' => 'db',
    'tableName' => '{{%queue}}',
    'channel' => 'default',
    'mutex' => PgsqlMutex::class,
];
