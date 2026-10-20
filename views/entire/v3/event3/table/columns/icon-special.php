<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

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
      match (true) {
        $model instanceof Event3StatsSpecial => $model->special->name,
        $model instanceof Event3StatsWeapon => $model->weapon->special->name,
      },
    ),
  ],
  'format' => 'raw',
  'value' => fn (Event3StatsSpecial|Event3StatsWeapon $model): string => Icon::s3special(
    match (true) {
      $model instanceof Event3StatsSpecial => $model->special,
      $model instanceof Event3StatsWeapon => $model->weapon->special,
    },
  ),
];
