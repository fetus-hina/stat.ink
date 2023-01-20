<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_death,
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'float'],
  'label' => Yii::t('app', 'Avg Deaths'),
  'value' => fn (StatWeapon3Usage $model): string => Html::tag(
    'span',
    Html::encode(Yii::$app->formatter->asDecimal($model->avg_death, 2)),
    [
      'class' => 'auto-tooltip',
      'title' => $model->sd_death === null ? '' : sprintf('σ=%.2f', (float)$model->sd_death),
    ],
  ),
];
