<?php

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon3', 'Salmometer'),
  'contentOptions' => ['class' => 'cell-king-smell text-center'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-king-smell'],
  'label' => '',
  'value' => function (Salmon3 $model): ?string {
    $meter = $model->king_smell;
    if ($meter === null) {
      return null;
    }

    return vsprintf('%s / %s', [
      Yii::$app->formatter->asInteger($meter),
      Yii::$app->formatter->asInteger(5),
    ]);
  },
];
