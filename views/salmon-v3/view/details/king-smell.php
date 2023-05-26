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

    return Progress::widget([
      'percent' => 100 * $model->king_smell / 5,
      'label' => vsprintf('%s / %s', [
        Yii::$app->formatter->asInteger($model->king_smell),
        Yii::$app->formatter->asInteger(5),
      ]),
      'barOptions' => [
        'class' => 'progress-bar-warning',
      ],
    ]);
    return vsprintf('%s (%s / %s)', [
      Html::img(
        $asset->getIconUrl($model->king_smell, 'yokozuna'),
        ['class' => 'basic-icon'],
      ),
      Yii::$app->formatter->asInteger($model->king_smell),
      Yii::$app->formatter->asInteger(5),
    ]);
  },
];
