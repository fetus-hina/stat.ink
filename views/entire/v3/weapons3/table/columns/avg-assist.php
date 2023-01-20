<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_assist,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'label' => Yii::t('app', 'Avg Assists'),
  'value' => fn (StatWeapon3Usage $model): string => Html::tag(
    'span',
    Html::encode(Yii::$app->formatter->asDecimal($model->avg_assist, 2)),
    [
      'class' => 'auto-tooltip',
      'title' => $model->sd_assist === null ? '' : sprintf('σ=%.2f', (float)$model->sd_assist),
    ],
  ),
];
