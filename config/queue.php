<?php
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
