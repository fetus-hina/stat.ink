<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
