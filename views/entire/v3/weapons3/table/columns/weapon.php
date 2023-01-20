<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use app\components\widgets\v3\WeaponName;

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'data-sort-value' => Yii::t('app-weapon3', $model->weapon->name),
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'string'],
  'label' => Yii::t('app', 'Weapon'),
  'value' => fn (StatWeapon3Usage $model): string => WeaponName::widget([
    'model' => $model->weapon,
    'showName' => true,
    'subInfo' => false,
  ]),
];
