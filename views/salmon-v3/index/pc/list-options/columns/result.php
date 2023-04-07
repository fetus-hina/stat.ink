<?php

declare(strict_types=1);

use app\components\widgets\Label;
use app\models\Salmon3;
use yii\grid\Column;

return [
  'label' => Yii::t('app', 'Result'),
  'headerOptions' => ['class' => 'cell-result'],
  'contentOptions' => ['class' => 'cell-result nobr'],
  'format' => 'raw',
  'value' => function (Salmon3 $model, $key, $index, Column $column): ?string {
    $clearWaves = $model->clear_waves;
    if ($clearWaves === null) {
      return null;
    }

    $expectWaves = $model->is_eggstra_work ? 5 : 3;
    if ($clearWaves >= $expectWaves) {
      if (
        $model->is_eggstra_work ||
        $model->clear_extra === null ||
        !$king = $model->kingSalmonid
      ) {
        return Label::widget([
          'color' => 'success',
          'content' => Yii::t('app-salmon2', 'Cleared'),
        ]);
      }

      return implode(' ', [
        Label::widget([
          'color' => 'success',
          'content' => Yii::t('app-salmon2', 'Cleared'),
        ]),
        Label::widget([
          'color' => $model->clear_extra ? 'success' : 'danger',
          'content' => vsprintf('%s: %s', [
            Yii::t('app-salmon2', $model->clear_extra ? '✓' : '✗'),
            Yii::t('app-salmon-boss3', $king->name),
          ]),
        ]),
      ]);
    }

    return trim(
      implode(' ', [
        Label::widget([
          'color' => 'danger',
          'content' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
            'waveNumber' => $column->grid->formatter->asInteger($clearWaves + 1),
          ]),
        ]),
        $model->fail_reason_id
          ? Label::widget([
            'color' => $model->failReason->color,
            'content' => Yii::t('app-salmon2', $model->failReason->short_name),
            'options' => [
              'class' => ['auto-tooltip'],
              'title' => Yii::t('app-salmon2', $model->failReason->name),
            ],
          ])
          : '',
        ]),
      );
  },
];
