<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): array => [
    'data-sort-value' => Yii::t('app-special3', $model->weapon?->special?->name ?? ''),
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'string'],
  'label' => '',
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): string => (string)Icon::s3Special(
    $model->weapon?->special,
  ),
];
