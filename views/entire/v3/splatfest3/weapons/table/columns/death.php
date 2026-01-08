<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use app\models\Splatfest3StatsWeapon;
use yii\base\Model;
use yii\grid\GridView;
use yii\helpers\Html;

return [
  'contentOptions' => fn (Splatfest3StatsWeapon $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_death,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'label' => Yii::t('app', 'Avg Deaths'),
  'value' => fn (Splatfest3StatsWeapon $model): string => BattleSummaryItemWidget::widget([
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
      Yii::t('app-weapon3', $model->weapon->name),
      Yii::t('app', 'Avg Deaths'),
    ]),
    'tooltipText' => '',
    'total' => $model->battles * $model->avg_death,
  ]),
];
