<?php

/**
 * @copyright Copyright (C) 2025-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\Icon;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Series Weapon Power'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $before = $model->series_weapon_power_before;
    $after = $model->series_weapon_power_after;
    if ($before === null && $after === null) {
      return null;
    }

    $weaponIcon = Icon::s3Weapon($model->weapon) ?? Icon::s3SalmonRandomRandom();

    $f = Yii::createObject([
      'class' => Formatter::class,
      'nullDisplay' => Icon::unknown(),
    ]);

    if (
      abs((float)$before - (float)$after) < 0.1 ||
      $after === null
    ) {
      return trim(
        implode(' ', [
          $weaponIcon,
          $f->asDecimal($before, 1),
        ]),
      );
    }

    return trim(
      implode(' ', [
        $weaponIcon,
        $f->asDecimal($before, 1),
        Icon::arrowRight(),
        $f->asDecimal($after, 1),
      ]),
    );
  },
];
