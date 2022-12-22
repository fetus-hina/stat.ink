<?php

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\FA;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Level'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->level_before === null && $model->level_after === null) {
      return null;
    }

    $f = Yii::createObject([
      'class' => Formatter::class,
      'nullDisplay' => (string)FA::fas('question')->fw(),
    ]);

    return implode('', [
      $f->asInteger($model->level_before),
      (string)FA::fas('arrow-right')->fw(),
      $f->asInteger($model->level_after),
    ]);
  },
];
