<?php

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\Icon;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Cash'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->cash_before === null && $model->cash_after === null) {
      return null;
    }

    $f = Yii::createObject([
      'class' => Formatter::class,
      'nullDisplay' => Icon::unknown(),
    ]);

    return implode(' ', [
      $f->asInteger($model->cash_before),
      Icon::arrowRight(),
      $f->asInteger($model->cash_after),
    ]);
  },
];
