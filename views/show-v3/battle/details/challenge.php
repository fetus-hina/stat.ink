<?php

declare(strict_types=1);

use app\models\Battle3;
use yii\bootstrap\Html;

return [
  'label' => Yii::t('app', 'Series Progress'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->challenge_win === null || $model->challenge_lose === null) {
      return null;
    }

    $win = \min(\max($model->challenge_win, 0), 5);
    $lose = \min(\max($model->challenge_lose, 0), 3);

    return \implode('', [
      Html::tag(
        'div',
        \implode('', [
          Html::tag(
            'span',
            \str_repeat(
              Html::tag('span', '', ['class' => 'fas fa-fw fa-circle']),
              $win
            ),
            ['class' => 'text-success']
          ),
          Html::tag(
            'span',
            \str_repeat(
              Html::tag('span', '', ['class' => 'fas fa-fw fa-circle']),
              5 - $win
            ),
            [
              'class' => 'text-muted',
              'style' => 'opacity:0.2',
            ]
          ),
        ]),
        ['class' => 'series-progress'],
      ),
      Html::tag(
        'div',
        \implode('', [
          Html::tag(
            'span',
            \str_repeat(
              Html::tag('span', '', ['class' => 'fas fa-fw fa-square']),
              3 - $lose
            ),
            ['class' => 'text-warning']
          ),
          Html::tag(
            'span',
            \str_repeat(
              Html::tag('span', '', ['class' => 'fas fa-fw fa-times']),
              $lose
            ),
            [
              'class' => 'text-danger',
              'style' => 'opacity:0.5',
            ]
          ),
        ]),
        ['class' => 'series-progress'],
      ),
    ]);
  },
];
