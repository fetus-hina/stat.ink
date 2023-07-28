<?php

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;
use yii\base\Model;
use yii\grid\GridView;
use yii\helpers\Html;

return [
  'contentOptions' => fn (Event3StatsSpecial|Event3StatsWeapon $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_inked,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'label' => Yii::t('app', 'Avg Inked'),
  'value' => fn (Event3StatsSpecial|Event3StatsWeapon $model): string => BattleSummaryItemWidget::widget([
    'battles' => $model->battles,
    'max' => $model->max_inked,
    'median' => $model->p50_inked,
    'min' => $model->min_inked,
    'pct5' => $model->p05_inked,
    'pct95' => $model->p95_inked,
    'q1' => $model->p25_inked,
    'q3' => $model->p75_inked,
    'stddev' => $model->sd_inked,
    'summary' => vsprintf('%s - %s', [
      match ($model::class) {
        Event3StatsSpecial::class => Yii::t('app-special3', $model->special->name),
        Event3StatsWeapon::class => Yii::t('app-weapon3', $model->weapon->name),
      },
      Yii::t('app', 'Avg Inked'),
    ]),
    'tooltipText' => '',
    'total' => $model->battles * $model->avg_inked,
  ]),
];
