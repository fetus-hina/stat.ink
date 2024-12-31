<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use app\models\StatWeapon3XUsagePerVersion;
use yii\base\Model;
use yii\grid\GridView;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $model): array => [
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
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $model): string => BattleSummaryItemWidget::widget([
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
        Yii::t('app-weapon3', $model->weapon?->name ?? ''),
        Yii::t('app', 'Avg Inked'),
    ]),
    'tooltipText' => '',
    'total' => $model->battles * $model->avg_inked,
  ]),
];
