<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Cash'),
  'value' => function (Battle3 $model): ?string {
    if ($model->cash_before === null && $model->cash_after === null) {
      return null;
    }

    return \vsprintf('%s → %s', [
      $model->cash_before !== null
        ? Yii::$app->formatter->asInteger($model->cash_before)
        : '?',
      $model->cash_after !== null
        ? Yii::$app->formatter->asInteger($model->cash_after)
        : '?',
    ]);
  },
];
