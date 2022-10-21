<?php

declare(strict_types=1);

use app\models\Battle3;
use yii\web\View;

/**
 * @var View $this
 */

return fn (Battle3 $model): array => [
  'class' => array_filter(
    [
      'battle-row',
      $model->has_disconnect ? 'disconnected' : null,
    ],
    fn (?string $v): bool => $v !== null,
  ),
];
