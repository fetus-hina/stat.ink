<?php

declare(strict_types=1);

use app\models\Event3StatsWeapon;

return [
  'label' => Yii::t('app', 'Weapon'),
  'headerOptions' => [
    'data-sort' => 'string',
    'data-sort-default' => 'asc',
  ],
  'contentOptions' => fn (Event3StatsWeapon $model): array => [
    'class' => 'auto-tooltip',
    'title' => vsprintf('%s %s / %s %s', [
      Yii::t('app', 'Sub:'),
      Yii::t('app-subweapon3', $model->weapon->subweapon?->name ?? '?'),
      Yii::t('app', 'Special:'),
      Yii::t('app-special3', $model->weapon->special?->name ?? '?'),
    ]),
  ],
  'value' => fn (Event3StatsWeapon $model): string => Yii::t('app-weapon3', $model->weapon->name),
];
