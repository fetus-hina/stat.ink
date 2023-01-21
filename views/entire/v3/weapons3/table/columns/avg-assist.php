<?php

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use yii\base\Model;
use yii\grid\GridView;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_assist,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')('avg_assist'),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'Avg Assists'),
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): string => BattleSummaryItemWidget::widget([
    'battles' => $model->battles,
    'max' => $model->max_assist,
    'median' => $model->p50_assist,
    'min' => $model->min_assist,
    'pct5' => $model->p05_assist,
    'pct95' => $model->p95_assist,
    'q1' => $model->p25_assist,
    'q3' => $model->p75_assist,
    'stddev' => $model->sd_assist,
    'summary' => vsprintf('%s - %s', [
        Yii::t('app-weapon3', $model->weapon?->name ?? ''),
        Yii::t('app', 'Avg Assists'),
    ]),
    'tooltipText' => '',
    'total' => $model->battles * $model->avg_assist,
  ]),
];
