<?php

declare(strict_types=1);

use app\components\widgets\Label;
use app\models\Salmon3;

return [
  'label' => Yii::t('app', 'Result'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if ($model->clear_waves === null) {
      return null;
    }

    $labels = [];
    if ($model->clear_waves > 2) {
      $labels[] = Label::widget([
        'color' => 'success',
        'content' => Yii::t('app-salmon2', 'Cleared'),
      ]);

      if ($model->kingSalmonid) {
        if (\is_bool($model->clear_extra)) {
          $labels[] = Label::widget([
            'color' => $model->clear_extra ? 'success' : 'danger',
            'content' => vsprintf('%s: %s', [
              Yii::t('app-salmon2', $model->clear_extra ? '✓' : '✗'),
              Yii::t('app-salmon-boss3', $model->kingSalmonid->name),
            ]),
          ]);
        } else {
          $labels[] = Label::widget([
            'color' => 'default',
            'content' => vsprintf('?: %s', [
              Yii::t('app-salmon-boss3', $model->kingSalmonid->name),
            ]),
          ]);
        }
      }
    } else {
      $labels[] = Label::widget([
        'color' => 'danger',
        'content' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
          'waveNumber' => Yii::$app->formatter->asInteger($model->clear_waves + 1),
        ]),
      ]);

      if ($model->failReason) {
        $labels[] = Label::widget([
          'color' => 'warning',
          'content' => Yii::t('app-salmon2', $model->failReason->name),
        ]);
      }
    }

    return implode(' ', $labels);
  },
];
