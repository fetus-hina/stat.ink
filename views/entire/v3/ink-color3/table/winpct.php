<?php

declare(strict_types=1);

use app\models\StatInkColor3;
use yii\bootstrap\Progress;

return [
  'format' => 'raw',
  'headerOptions' => [
    'class' => 'text-center',
    'width' => '50%',
  ],
  'label' => Yii::t('app', 'Win %'),
  'value' => function (StatInkColor3 $model): string {
    $f = Yii::$app->formatter;
    $battles = $model->battles;
    if ($battles < 1) {
      return '';
    }

    $rate1 = $model->wins / $battles;
    $rate2 = 1.0 - $rate1;

    // ref. http://lfics81.techblog.jp/archives/2982884.html
    $stderr = sqrt($battles / ($battles - 1.5) * $rate1 * $rate2) / sqrt($battles);
    $err95ci = $stderr * 1.96;
    $err99ci = $stderr * 2.58;

    return Progress::widget([
      'bars' => [
        [
          'label' => vsprintf('%s %s', [
            $f->asPercent($rate1, 1),
            match (true) {
              $rate1 - $err99ci >= 0.5 => '**',
              $rate1 + $err99ci <= 0.5 => '**',
              $rate1 - $err95ci >= 0.5 => '*',
              $rate1 + $err95ci <= 0.5 => '*',
              default => '',
            },
          ]),
          'percent' => ($rate1 - $err99ci) * 100,
          'options' => [
            'aria-valuemax' => null,
            'aria-valuemin' => null,
            'aria-valuenow' => null,
            'class' => 'auto-tooltip',
            'style' => [
              'background-color' => "#{$model->color1}",
              'width' => sprintf('%f%%', ($rate1 - $err99ci) * 100), 
            ],
            'title' => vsprintf('%s: %s～%s', [
              Yii::t('app', '{pct}% CI', ['pct' => 99]),
              $f->asPercent(max(0, $rate1 - $err99ci), 2),
              $f->asPercent(min(100, $rate1 + $err99ci), 2),
            ]),
          ],
        ],
        [
          'label' => '',
          'percent' => $err99ci * 2 * 100,
          'options' => [
            'aria-valuemax' => null,
            'aria-valuemin' => null,
            'aria-valuenow' => null,
            'style' => [
              'background-color' => "#ccc",
              'width' => sprintf('%f%%', $err99ci * 2 * 100),
            ],
          ],
        ],
        [
          'label' => vsprintf('%s %s', [
            $f->asPercent($rate2, 1),
            match (true) {
              $rate2 - $err99ci >= 0.5 => '**',
              $rate2 + $err99ci <= 0.5 => '**',
              $rate2 - $err95ci >= 0.5 => '*',
              $rate2 + $err95ci <= 0.5 => '*',
              default => '',
            },
          ]),
          'percent' => ($rate2 - $err99ci) * 100,
          'options' => [
            'aria-valuemax' => null,
            'aria-valuemin' => null,
            'aria-valuenow' => null,
            'class' => 'auto-tooltip',
            'style' => [
              'background-color' => "#{$model->color2}",
              'width' => sprintf('%f%%', ($rate2 - $err99ci) * 100),
            ],
            'title' => vsprintf('%s: %s～%s', [
              Yii::t('app', '{pct}% CI', ['pct' => 99]),
              $f->asPercent(max(0, $rate2 - $err99ci), 2),
              $f->asPercent(min(100, $rate2 + $err99ci), 2),
            ]),
          ],
        ],
      ],
    ]);
  },
];
