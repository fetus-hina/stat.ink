<?php

declare(strict_types=1);

use app\components\widgets\KillRatioBadgeWidget;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use yii\helpers\Html;

$ratio = fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): ?float => $model->avg_death > 0
  ? ($model->avg_kill + $model->avg_assist) / $model->avg_death
  : null;

return [
  'contentOptions' => function (StatWeapon3Usage|StatWeapon3UsagePerVersion $model) use ($ratio): array {
    $kr = $ratio($model);
    return [
      'class' => 'text-right',
      'data-sort-value' => $kr === null ? '0' : $kr,
    ];
  },
  'format' => ['decimal', 3],
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'filter' => (require __DIR__ . '/includes/correlation-filter.php')($ratio),
  'filterOptions' => ['class' => 'text-right'],
  'label' => Yii::t('app', 'K+A/D'),
  'value' => $ratio,
];
