<?php

declare(strict_types=1);

use app\models\Battle3;
use app\models\User;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\DetailView;

return [
  'label' => Yii::t('app', 'Final Count'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->our_team_count !== null && $model->their_team_count !== null) {
      $ourCount = (int)$model->our_team_count;
      $theirCount = (int)$model->their_team_count;
      if ($ourCount > 0 || $theirCount > 0) {
        $ourDrawPct = round($ourCount * 100 / ($ourCount + $theirCount) * 100) / 100;
        return Html::tag(
          'div',
          implode('', [
            Html::tag(
              'div',
              Html::encode(Yii::$app->formatter->asInteger($ourCount)),
              [
                'class' => ['progress-bar', 'progress-bar-info'],
                'style' => array_merge(
                  ['width' => sprintf('%.2f%%', $ourDrawPct)],
                  $model->our_team_color
                    ? ['background-color' => "#{$model->our_team_color}"]
                    : [],
                ),
              ]
            ),
            Html::tag(
              'div',
              Html::encode(Yii::$app->formatter->asInteger($theirCount)),
              [
                'class' => ['progress-bar', 'progress-bar-danger'],
                'style' => array_merge(
                  ['width' => sprintf('%.2f%%', 100 - $ourDrawPct)],
                  $model->their_team_color
                    ? ['background-color' => "#{$model->their_team_color}"]
                    : [],
                ),
              ]
            )
          ]),
          ['class' => 'progress', 'style' => 'width:100%;max-width:400px']
        );
      }
    }
    return null;
  },
];
