<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;

return [
  'label' => '',
  'headerOptions' => [
    'data-sort' => 'string',
    'data-sort-default' => 'asc',
  ],
  'contentOptions' => fn (Event3StatsSpecial|Event3StatsWeapon $model): array => [
    'data-sort-value' => Yii::t(
      'app-special3',
      match ($model::class) {
        Event3StatsSpecial::class => $model->special->name,
        Event3StatsWeapon::class => $model->weapon->special->name,
      },
    ),
  ],
  'format' => 'raw',
  'value' => fn (Event3StatsSpecial|Event3StatsWeapon $model): string => Icon::s3special(
    match ($model::class) {
      Event3StatsSpecial::class => $model->special,
      Event3StatsWeapon::class => $model->weapon?->special,
    },
  ),
];
