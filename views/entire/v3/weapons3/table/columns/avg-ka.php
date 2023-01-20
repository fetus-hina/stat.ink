<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_kill + $model->avg_assist,
  ],
  'format' => ['decimal', 2],
  'headerOptions' => ['data-sort' => 'float'],
  'label' => Yii::t('app', 'Avg K+A'),
  'value' => fn (StatWeapon3Usage $model): float => $model->avg_kill + $model->avg_assist,
];
