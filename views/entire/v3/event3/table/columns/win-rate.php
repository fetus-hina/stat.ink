<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
use app\components\helpers\TypeHelper;
use yii\base\Model;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;

/**
 * @var float $maxWinRate
 */

return [
  'label' => Yii::t('app', 'Win %'),
  'format' => 'raw',
  'value' => function (Model $model) use ($maxWinRate): string {
    $wins = TypeHelper::intOrNull(ArrayHelper::getValue($model, 'wins'));
    $battles = TypeHelper::intOrNull(ArrayHelper::getValue($model, 'battles'));
    if ($battles < 1) {
      return '';
    }

    $fmt = Yii::$app->formatter;

    $info = StandardError::winpct($wins, $battles);
    $rate = $info ? $info['rate'] : $wins / $battles;
    return Progress::widget([
      'percent' => min(1.0, $rate / $maxWinRate) * 100,
      'label' => trim(
        vsprintf('%s %s', [
          $fmt->asPercent($rate, 2),
          $info ? $info['significant'] : '',
        ]),
      ),
      'options' => [
        'class' => 'auto-tooltip',
        'style' => [
          'min-width' => '100px',
        ],
        'title' => $info
          ? Yii::t('app', '{from} - {to}', [
            'from' => $fmt->asPercent($info['min99ci'], 2),
            'to' => $fmt->asPercent($info['max99ci'], 2),
          ])
          : '',
      ],
    ]);
  },
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'contentOptions' => function (Model $model): array {
    $wins = TypeHelper::intOrNull(ArrayHelper::getValue($model, 'wins'));
    $battles = TypeHelper::intOrNull(ArrayHelper::getValue($model, 'battles'));

    return [
      'data' => [
        'sort-value' => $battles < 1 ? -1 : ($wins / $battles),
      ],
    ];
  },
];
