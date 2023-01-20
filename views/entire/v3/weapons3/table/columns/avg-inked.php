<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_inked,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')('avg_inked'),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'Avg Inked'),
  'value' => fn (StatWeapon3Usage $model): string => Html::tag(
    'span',
    Html::encode(Yii::$app->formatter->asDecimal($model->avg_inked, 1)),
    [
      'class' => 'auto-tooltip',
      'title' => $model->sd_inked === null ? '' : sprintf('σ=%.1f', (float)$model->sd_inked),
    ],
  ),
];
