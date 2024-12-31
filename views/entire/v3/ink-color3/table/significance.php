<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\StatInkColor3;
use yii\helpers\Html;

return [
  'format' => 'raw',
  'contentOptions' => [
    'class' => 'text-center',
  ],
  'headerOptions' => [
    'class' => 'auto-tooltip text-center omit',
    'title' => Yii::t('app', '{pct}% Significant?', ['pct' => 99]),
    'style' => [
      'max-width' => '5em',
      'width' => '5em',
    ],
  ],
  'label' => Yii::t('app', 'Significant?'),
  'value' => function (StatInkColor3 $model): string {
    $f = Yii::$app->formatter;
    $battles = $model->battles;
    if ($battles < 1) {
      return '';
    }

    // ref. http://lfics81.techblog.jp/archives/2982884.html
    $rate = $model->wins / $battles;
    $stderr = sqrt($battles / ($battles - 1.5) * $rate * (1 - $rate)) / sqrt($battles);
    $err99ci = $stderr * 2.58;
    $min = $rate - $err99ci;
    $max = $rate + $err99ci;
    
    return $min > 0.5 || $max < 0.5
      ? Html::tag('span', Icon::check(), ['class' => 'text-success'])
      : '';
  },
];
