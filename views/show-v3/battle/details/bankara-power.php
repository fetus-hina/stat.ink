<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\Icon;
use app\models\Battle3;

return [
  'label' => Yii::t('app-show-v3', 'Anarchy Power'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $before = $model->bankara_power_before;
    $after = $model->bankara_power_after;
    if ($before === null && $after === null) {
      return null;
    }

    $f = Yii::createObject([
      'class' => Formatter::class,
      'nullDisplay' => Icon::unknown(),
    ]);

    if (
        abs((float)$before - (float)$after) < 0.1 ||
        $after === null
    ) {
        return $f->asPower($before);
    }

    return implode(' ', [
      $f->asPower($before),
      Icon::arrowRight(),
      $f->asPower($after),
    ]);
  },
];
