<?php

declare(strict_types=1);

use app\models\Salmon3;
use yii\bootstrap\Progress;
use yii\helpers\Html;

return [
  'label' => Yii::t('app-salmon3', 'Salmometer'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if ($model->king_smell === null) {
      return null;
    }

    $f = Yii::$app->formatter;
    return Progress::widget([
      'percent' => 100 * (1 + $model->king_smell) / 6,
      'label' => vsprintf('%s / %s', [
        $f->asInteger($model->king_smell),
        $f->asInteger(5),
      ]),
      'barOptions' => [
        'class' => 'auto-tooltip progress-bar-warning',
        'title' => Yii::t('app-salmon3', 'It would appear at {percent} if all four were {smell}.', [
          'percent' => $f->asPercent(
            match ($model->king_smell) {
              0, 1 => 0.0,
              2 => 0.1,
              3 => 0.3,
              4 => 0.6,
              default => 1.0,
            },
            0,
          ),
          'smell' => $f->asInteger($model->king_smell),
        ]),
      ],
    ]);
  },
];
