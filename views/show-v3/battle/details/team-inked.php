<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
    if ($model->our_team_count !== null && $model->their_team_count !== null) {
      return null;
    }

    if ($model->our_team_percent !== null && $model->their_team_percent !== null) {
      $ourPct = (float)$model->our_team_percent;
      $theirPct = (float)$model->their_team_percent;
      $thirdPct = (float)$model->third_team_percent;
      if ($ourPct > 0 || $theirPct > 0 || $thirdPct > 0) {
        $totalPct = $ourPct + $theirPct + $thirdPct;
        $ourDrawPct = round(10000 * $ourPct / $totalPct) / 100;
        $thirdDrawPct = round(10000 * $thirdPct / $totalPct) / 100;
        $theirDrawPct = 100 - ($ourDrawPct + $thirdDrawPct);
        $ourPoint = null;
        $theirPoint = null;
        $thirdPoint = null;
        if (
          $model->our_team_inked !== null &&
          $model->their_team_inked !== null
        ) {
          $ourPoint = Yii::t('app', '{point}p', ['point' => $model->our_team_inked]);
          $theirPoint = Yii::t('app', '{point}p', ['point' => $model->their_team_inked]);
          $thirdPoint = $model->third_team_inked !== null
            ? Yii::t('app', '{point}p', ['point' => $model->third_team_inked])
            : null;
        } elseif ($model->map && $model->map->area !== null) {
          $ourPoint = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round($model->map->area * $ourPct / 100)),
          ]);
          $theirPoint = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round($model->map->area * $theirPct / 100)),
          ]);
          $thirdPoint = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round($model->map->area * $thirdPct / 100)),
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
                'style' => array_merge(
                  ['width' => sprintf('%.2f%%', $ourDrawPct)],
                  $model->our_team_color
                    ? ['background-color' => "#{$model->our_team_color}"]
                    : [],
                ),
                'title' => $ourPoint,
              ]
            ),
            Html::tag(
              'div',
              Html::encode(Yii::$app->formatter->asPercent($theirPct / 100, 1)),
              [
                'class' => ['progress-bar', 'progress-bar-danger'],
                'style' => array_merge(
                  ['width' => sprintf('%.2f%%', $theirDrawPct)],
                  $model->their_team_color
                    ? ['background-color' => "#{$model->their_team_color}"]
                    : [],
                ),
                'title' => $theirPoint,
              ]
            ),
            $model->rule?->key === 'tricolor'
              ? Html::tag(
                'div',
                Html::encode(Yii::$app->formatter->asPercent($thirdPct / 100, 1)),
                [
                  'class' => ['progress-bar', 'progress-bar-warning'],
                  'style' => array_merge(
                    ['width' => sprintf('%.2f%%', $thirdDrawPct)],
                    $model->third_team_color
                      ? ['background-color' => "#{$model->third_team_color}"]
                      : [],
                  ),
                  'title' => $thirdPoint,
                ],
              )
              : '',
          ]),
          [
            'class' => 'progress',
            'style' => 'width:100%;max-width:400px',
          ]
        );
      }
    }

    if (
      $model->our_team_inked !== null &&
      $model->their_team_inked !== null
    ) {
      $ourPoint = (int)$model->our_team_inked;
      $theirPoint = (int)$model->their_team_inked;
      $thirdPoint = (int)$model->third_team_inked;
      if ($ourPoint > 0 || $theirPoint > 0 || $thirdPoint > 0) {
        $totalPoint = $ourPoint + $theirPoint + $thirdPoint;
        $ourDrawPct = round($ourPoint * 100 / $totalPoint * 100) / 100;
        $thirdDrawPct = round($thirdPoint * 100 / $totalPoint * 100) / 100;
        $theirDrawPct = 100 - ($ourDrawPct + $thirdDrawPct);
        return Html::tag(
          'div',
          implode('', [
            Html::tag(
              'div',
              Html::encode(Yii::t('app', '{point}p', ['point' => $ourPoint])),
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
              Html::encode(Yii::t('app', '{point}p', ['point' => $theirPoint])),
              [
                'class' => ['progress-bar', 'progress-bar-danger', 'text-right'],
                'style' => array_merge(
                  ['width' => sprintf('%.2f%%', $theirDrawPct)],
                  $model->their_team_color
                    ? ['background-color' => "#{$model->their_team_color}"]
                    : [],
                ),
                'title' => $theirPoint,
              ]
            ),
            $model->rule?->key === 'tricolor'
              ? Html::tag(
                'div',
                Html::encode(Yii::t('app', '{point}p', ['point' => $thirdPoint])),
                [
                  'class' => ['progress-bar', 'progress-bar-danger', 'text-right'],
                  'style' => array_merge(
                    ['width' => sprintf('%.2f%%', $thirdDrawPct)],
                    $model->third_team_color
                      ? ['background-color' => "#{$model->third_team_color}"]
                      : [],
                  ),
                  'title' => $thirdPoint,
                ]
              )
              : '',
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
