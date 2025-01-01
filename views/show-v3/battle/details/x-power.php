<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\Icon;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'X Power'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $before = $model->x_power_before;
    $after = $model->x_power_after;
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
        return $f->asDecimal($before, 1);
    }

    return implode(' ', [
      $f->asDecimal($before, 1),
      Icon::arrowRight(),
      $f->asDecimal($after, 1),
    ]);
  },
];
