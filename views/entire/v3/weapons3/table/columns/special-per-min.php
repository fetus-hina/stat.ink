<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;

$calc = fn (StatWeapon3Usage $model): ?float => $model->seconds > 0 && $model->battles > 0
  ? $model->avg_special / ($model->seconds / $model->battles) * 60.0
  : null;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $calc($model),
  ],
  'format' => ['decimal', 3],
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')($calc),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'Specials/min'),
  'value' => fn (StatWeapon3Usage $model): ?float => $calc($model),
];
