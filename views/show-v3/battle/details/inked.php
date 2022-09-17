<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Turf Inked'),
  'value' => function (Battle3 $model): ?string {
    if ($model->inked === null) {
      return null;
    }

    return Yii::t('app', '{point}p', [
      'point' => Yii::$app->formatter->asInteger($model->inked),
    ]);
  },
];
