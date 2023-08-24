<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): array => [
    'data-sort-value' => Yii::t('app-subweapon3', $model->weapon?->subweapon?->name ?? ''),
  ],
  'headerOptions' => ['data-sort' => 'string'],
  'label' => '',
  'format' => 'raw',
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): string => (string)Icon::s3Subweapon(
    $model->weapon?->subweapon,
  ),
];
