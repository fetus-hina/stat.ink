<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Special'),
  'value' => function (Battle3 $model): ?string {
    if ($model->special === null) {
      return null;
    }

    $weapon = $model->weapon;
    if ($weapon) {
      $special = $weapon->special;
      if ($special) {
        return vsprintf('%s (%s)', [
          Yii::$app->formatter->asInteger($model->special),
          Yii::t('app-special3', $special->name),
        ]);
      }
    }

    return Yii::$app->formatter->asInteger($model->special);
  },
];
