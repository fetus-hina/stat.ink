<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3;
use yii\bootstrap\Progress;
use yii\helpers\Html;

return [
  'label' => Yii::t('app-salmon3', 'Salmometer'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if (
        $model->is_eggstra_work ||
        $model->is_private ||
        $model->king_smell === null
    ) {
      return null;
    }

    $f = Yii::$app->formatter;
    return Html::tag(
      'span',
      implode(' ', [
        Html::tag(
          'span',
          Icon::s3Salmometer(
            $model->king_smell,
            // オカシラゲージの個別表示は v6.0.0 から
            version_compare($model->version?->tag ?? '0.0.0', '6.0.0', '>=')
              ? $model->schedule?->king
              : null,
          ),
          ['style' => 'font-size: 2em'],
        ),
        vsprintf('(%s / %s)', [
          $f->asInteger($model->king_smell),
          $f->asInteger(5),
        ]),
      ]),
      [
        'class' => 'auto-tooltip',
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
    );
  },
];
