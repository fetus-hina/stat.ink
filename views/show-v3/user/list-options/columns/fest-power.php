<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;

$f = Yii::$app->formatter;

return [
  '-label' => Yii::t('app', 'Power (After)'),
  'contentOptions' => ['class' => 'cell-fest-power text-right nobr'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-fest-power'],
  'label' => Yii::t('app', 'Power'),
  'value' => fn (Battle3 $model): ?string => match (true) {
    $model->fest_power !== null && $model->fest_power >= 0.1 => vsprintf('%s %s', [
      Icon::s3LobbySplatfest(),
      $f->asDecimal((float)$model->fest_power, 1),
    ]),
    $model->bankara_power_after !== null && $model->bankara_power_after >= 0.1 => vsprintf('%s %s', [
      Icon::s3LobbyBankara(),
      $f->asDecimal((float)$model->bankara_power_after, 1),
    ]),
    $model->series_weapon_power_after !== null && $model->series_weapon_power_after >= 0.1 => vsprintf('%s %s', [
      Icon::s3Weapon($model?->weapon, alt: Yii::t('app', 'Series Weapon Power')),
      $f->asDecimal((float)$model->series_weapon_power_after, 1),
    ]),
    $model->event_power !== null && $model->event_power >= 0.1 => vsprintf('%s %s', [
      Icon::s3LobbyEvent(),
      $f->asDecimal((float)$model->event_power, 1),
    ]),
    default => null,
  },
];
