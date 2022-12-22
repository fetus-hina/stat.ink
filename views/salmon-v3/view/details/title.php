<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\models\Salmon3;
use app\models\SalmonTitle3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Title'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if (!$model->titleBefore && !$model->titleAfter) {
      return null;
    }

    $f = fn (?SalmonTitle3 $title, ?int $exp): string => ($title === null)
      ? (string)FA::fas('question')
      : trim(
        implode(' ', [
          Html::encode(Yii::t('app-salmon-title3', $title->name)),
          $exp === null ? '' : Yii::$app->formatter->asInteger($exp),
        ]),
      );

    return vsprintf('%1$s %3$s %2$s', [
      $f($model->titleBefore, $model->title_exp_before),
      $f($model->titleAfter, $model->title_exp_after),
      (string)FA::fas('arrow-right'),
    ]);
  },
];
