<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_special,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')('avg_special'),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'Avg Specials'),
  'value' => fn (StatWeapon3Usage $model): string => Html::tag(
    'span',
    Html::encode(Yii::$app->formatter->asDecimal($model->avg_special, 2)),
    [
      'class' => 'auto-tooltip',
      'title' => $model->sd_special === null ? '' : sprintf('σ=%.2f', (float)$model->sd_special),
    ],
  ),
];
