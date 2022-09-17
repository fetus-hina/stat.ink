<?php

declare(strict_types=1);

use app\components\widgets\Label;
use app\models\Battle3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'Result'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    $result = $model->result;
    if (!$result) {
      return null;
    }

    $rule = $model->rule;

    $parts = [];
    if ($rule && $rule->key !== 'nawabari' && $model->is_knockout !== null) {
      $parts[] = $model->is_knockout
        ? Label::widget([
          'content' => Yii::t('app', 'Knockout'),
          'color' => 'info',
        ])
        : Label::widget([
          'content' => Yii::t('app', 'Time is up'),
          'color' => 'warning',
        ]);
    }

    $parts[] = Label::widget([
      'content' => Yii::t('app', $result->name),
      'color' => $result->label_color,
    ]);

    return \implode(' ', $parts);
  },
];
