<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\StatWeapon3Usage;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'data-sort-value' => Yii::t('app-weapon3', $model->weapon?->special?->name ?? ''),
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'string'],
  'label' => '',
  'value' => fn (StatWeapon3Usage $model): string => SpecialIcon::widget([
    'model' => $model->weapon?->special,
  ]),
];
