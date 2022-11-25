<?php

declare(strict_types=1);

use app\models\Salmon3;

return [
  'contentOptions' => ['class' => 'cell-title-after'],
  'headerOptions' => ['class' => 'cell-title-after'],
  'label' => Yii::t('app', 'Title (After)'),
  'value' => function (Salmon3 $model): ?string {
    if (!$model->title_after_id) {
      return null;
    }

    return implode(' ', [
      Yii::t('app-salmon-title3', $model->titleAfter->name),
      $model->title_exp_after === null
        ? ''
        : Yii::$app->formatter->asInteger($model->title_exp_after),
    ]);
  },
];
