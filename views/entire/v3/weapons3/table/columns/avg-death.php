<?php

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use yii\base\Model;
use yii\grid\GridView;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_death,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')('avg_death'),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'Avg Deaths'),
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage $model): string => BattleSummaryItemWidget::widget([
    'battles' => $model->battles,
    'max' => $model->max_death,
    'median' => $model->p50_death,
    'min' => $model->min_death,
    'pct5' => $model->p05_death,
    'pct95' => $model->p95_death,
    'q1' => $model->p25_death,
    'q3' => $model->p75_death,
    'stddev' => $model->sd_death,
    'summary' => vsprintf('%s - %s', [
        Yii::t('app-weapon3', $model->weapon?->name ?? ''),
        Yii::t('app', 'Avg Deaths'),
    ]),
    'tooltipText' => '',
    'total' => $model->battles * $model->avg_death,
  ]),
];
