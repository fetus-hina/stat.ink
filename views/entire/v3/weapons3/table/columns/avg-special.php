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
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $model): string => BattleSummaryItemWidget::widget([
    'battles' => $model->battles,
    'max' => $model->max_special,
    'median' => $model->p50_special,
    'min' => $model->min_special,
    'pct5' => $model->p05_special,
    'pct95' => $model->p95_special,
    'q1' => $model->p25_special,
    'q3' => $model->p75_special,
    'stddev' => $model->sd_special,
    'summary' => vsprintf('%s - %s', [
        Yii::t('app-weapon3', $model->weapon?->name ?? ''),
        Yii::t('app', 'Avg Specials'),
    ]),
    'tooltipText' => '',
    'total' => $model->battles * $model->avg_special,
  ]),
];
