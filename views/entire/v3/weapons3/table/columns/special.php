<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage $model): array => [
    'data-sort-value' => Yii::t('app-special3', $model->weapon?->special?->name ?? ''),
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'string'],
  'label' => '',
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage $model): string => (string)Icon::s3Special(
    $model->weapon?->special,
  ),
];
