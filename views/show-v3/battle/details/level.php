<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Level'),
  'value' => function (Battle3 $model): ?string {
    if ($model->level_before === null && $model->level_after === null) {
      return null;
    }

    return \vsprintf('%s → %s', [
      $model->level_before !== null ? (string)(int)$model->level_before : '?',
      $model->level_after !== null ? (string)(int)$model->level_after : '?',
    ]);
  },
];
