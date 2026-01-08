<?php

/**
 * @copyright Copyright (C) 2025-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;

$f = Yii::$app->formatter;

return [
  'contentOptions' => ['class' => 'cell-fest-power-before text-right nobr'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-fest-power-before'],
  'label' => Yii::t('app', 'Power'),
  'value' => fn (Battle3 $model): ?string => match (true) {
    $model->bankara_power_before !== null && $model->bankara_power_before >= 0.1 => vsprintf('%s %s', [
      Icon::s3LobbyBankara(),
      $f->asDecimal((float)$model->bankara_power_before, 1),
    ]),
    $model->series_weapon_power_before !== null && $model->series_weapon_power_before >= 0.1 => vsprintf('%s %s', [
      Icon::s3Weapon($model?->weapon, alt: Yii::t('app', 'Series Weapon Power')),
      $f->asDecimal((float)$model->series_weapon_power_before, 1),
    ]),
    $model->x_power_before !== null && $model->x_power_before >= 0.1 => vsprintf('%s %s', [
      Icon::s3LobbyX(),
      $f->asDecimal((float)$model->x_power_before, 1),
    ]),
    default => null,
  },
];
