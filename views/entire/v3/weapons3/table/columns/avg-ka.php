<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use yii\helpers\Html;

$calc = fn (StatWeapon3Usage $model): float => $model->avg_kill + $model->avg_assist;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $calc($model),
  ],
  'format' => ['decimal', 2],
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')($calc),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'Avg K+A'),
  'value' => $calc,
];
