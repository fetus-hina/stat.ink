<?php

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\FA;
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
      'nullDisplay' => (string)FA::fas('question')->fw(),
    ]);

    if (
        abs((float)$before - (float)$after) < 0.1 ||
        $after === null
    ) {
        return $f->asDecimal($before, 1);
    }

    return vsprintf('%s%s%s', [
      $f->asDecimal($before, 1),
      (string)FA::fas('arrow-right')->fw(),
      $f->asDecimal($after, 1),
    ]);
  },
];
