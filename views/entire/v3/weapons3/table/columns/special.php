<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): array => [
    'data-sort-value' => Yii::t('app-special3', $model->weapon?->special?->name ?? ''),
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'string'],
  'label' => Yii::t('app', 'Special'),
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): string => Yii::t(
    'app-special3',
    $model->weapon?->special?->name ?? '',
  ),
];
