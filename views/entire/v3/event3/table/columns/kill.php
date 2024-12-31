<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;
use yii\helpers\Html;

return [
  'contentOptions' => fn (Event3StatsSpecial|Event3StatsWeapon $model): array => [
    'class' => 'text-right',
    'data-sort-value' => $model->avg_kill,
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'label' => Yii::t('app', 'Avg Kills'),
  'value' => fn (Event3StatsSpecial|Event3StatsWeapon $model): string => BattleSummaryItemWidget::widget([
    'battles' => $model->battles,
    'max' => $model->max_kill,
    'median' => $model->p50_kill,
    'min' => $model->min_kill,
    'pct5' => $model->p05_kill,
    'pct95' => $model->p95_kill,
    'q1' => $model->p25_kill,
    'q3' => $model->p75_kill,
    'stddev' => $model->sd_kill,
    'summary' => vsprintf('%s - %s', [
      match ($model::class) {
        Event3StatsSpecial::class => Yii::t('app-special3', $model->special->name),
        Event3StatsWeapon::class => Yii::t('app-weapon3', $model->weapon->name),
      },
      Yii::t('app', 'Avg Kills'),
    ]),
    'tooltipText' => '',
    'total' => $model->battles * $model->avg_kill,
  ]),
];
