<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): array => [
    'data-sort-value' => Yii::t('app-subweapon3', $model->weapon?->subweapon?->name ?? ''),
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'string'],
  'label' => '',
  'value' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): string => SubweaponIcon::widget([
    'model' => $model->weapon?->subweapon,
  ]),
];
