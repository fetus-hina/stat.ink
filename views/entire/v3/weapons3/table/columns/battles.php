<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;

return [
  'attribute' => 'battles',
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->battles,
  ],
  'format' => 'integer',
  'headerOptions' => [
    'data-sort' => 'int',
    'data-sort-default' => 'desc',
    'data-sort-onload' => 'yes',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')('battles'),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'Players'),
];
