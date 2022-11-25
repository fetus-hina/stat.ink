<?php

declare(strict_types=1);

use app\models\Salmon3;

return fn (Salmon3 $model): array => [
  'class' => array_filter([
    'battle-row',
    $model->has_disconnect ? 'disconnected' : null,
  ]),
];
