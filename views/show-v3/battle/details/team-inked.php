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
  'label' => Yii::t('app', 'Team Inked'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->our_team_percent !== null && $model->their_team_percent !== null) {
      $ourPct = (float)$model->our_team_percent;
      $theirPct = (float)$model->their_team_percent;
      if ($ourPct > 0 || $theirPct > 0) {
        $ourDrawPct = round($ourPct * 100 / ($ourPct + $theirPct) * 100) / 100;
        $ourPoint = null;
        $theirPoint = null;
        if ($model->our_team_inked !== null && $model->their_team_inked !== null) {
          $ourPoint = Yii::t('app', '{point}p', ['point' => $model->our_team_inked]);
          $theirPoint = Yii::t('app', '{point}p', ['point' => $model->their_team_inked]);
        } elseif ($model->map && $model->map->area !== null) {
          $ourPoint = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round($model->map->area * $ourPct / 100)),
          ]);
          $theirPoint = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round($model->map->area * $theirPct / 100)),
          ]);
        }
        return Html::tag(
          'div',
          implode('', [
            Html::tag(
              'div',
              Html::encode(Yii::$app->formatter->asPercent($ourPct / 100, 1)),
              [
                'class' => ['progress-bar', 'progress-bar-info'],
                'style' => ['width' => sprintf('%.2f%%', $ourDrawPct)],
                'title' => $ourPoint,
              ]
            ),
            Html::tag(
              'div',
              Html::encode(Yii::$app->formatter->asPercent($theirPct / 100, 1)),
              [
                'class' => ['progress-bar', 'progress-bar-danger', 'text-right'],
                'style' => ['width' => sprintf('%.2f%%', 100 - $ourDrawPct)],
                'title' => $theirPoint,
              ]
            )
          ]),
          [
            'class' => 'progress',
            'style' => 'width:100%;max-width:400px',
          ]
        );
      }
    }

    if ($model->our_team_inked !== null && $model->their_team_inked !== null) {
      $ourPoint = (int)$model->our_team_inked;
      $theirPoint = (int)$model->their_team_inked;
      if ($ourPoint > 0 || $theirPoint > 0) {
        $ourDrawPct = round($ourPoint * 100 / ($ourPoint + $theirPoint) * 100) / 100;
        return Html::tag(
          'div',
          implode('', [
            Html::tag(
              'div',
              Html::encode(Yii::t('app', '{point}p', ['point' => $ourPoint])),
              [
                'class' => ['progress-bar', 'progress-bar-info'],
                'style' => ['width' => sprintf('%.2f%%', $ourDrawPct)],
              ]
            ),
            Html::tag(
              'div',
              Html::encode(Yii::t('app', '{point}p', ['point' => $theirPoint])),
              [
                'class' => ['progress-bar', 'progress-bar-danger', 'text-right'],
                'style' => ['width' => sprintf('%.2f%%', 100 - $ourDrawPct)],
                'title' => $theirPoint,
              ]
            ),
          ]),
          [
            'class' => 'progress',
            'style' => 'width:100%;max-width:400px',
          ]
        );
      }
    }

    return null;
  },
];
