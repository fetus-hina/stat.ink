<?php

declare(strict_types=1);

use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;

return [
  'label' => Yii::t('app', 'Special'),
  'value' => fn (Event3StatsSpecial|Event3StatsWeapon $model): string => Yii::t(
    'app-special3',
    match ($model::class) {
      Event3StatsSpecial::class => $model->special->name,
      Event3StatsWeapon::class => $model->weapon?->special?->name ?? null,
    },
  ),
];
