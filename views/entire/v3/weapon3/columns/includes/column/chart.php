<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var Model[] $data
 * @var View $this
 * @var string $xLabel
 * @var string|string[]|callable $xGet
 */

$datasetBattles = [
  'type' => 'bar',
  'label' => Yii::t('app', 'Battles'),
  'data' => array_values(
    ArrayHelper::getColumn(
      $data,
      fn (Model $model): array => [
        'x' => (int)ArrayHelper::getValue($model, $xGet),
        'y' => $model->battles,
      ],
    ),
  ),
  'backgroundColor' => new JsExpression('window.colorScheme.graph2'),
  'yAxisID' => 'y2',
];

$winPctPoints = array_values(
  ArrayHelper::getColumn(
    $data,
    fn (Model $model): array => [
      'x' => (int)ArrayHelper::getValue($model, $xGet),
      'y' => 100.0 * $model->wins / $model->battles,
      'n' => $model->battles,
    ],
  ),
);

$datasetWinPct = [
  'type' => 'line',
  'label' => Yii::t('app', 'Win %'),
  'data' => array_values(array_filter($winPctPoints, fn (array $v): bool => $v['n'] >= 10)),
  'borderColor' => new JsExpression('window.colorScheme.graph1'),
  'backgroundColor' => new JsExpression('window.colorScheme.graph1'),
];

$datasetWinPctErrors = [
  'data' => array_values(
    array_filter(
      ArrayHelper::getColumn(
        $data,
        function (Model $model) use ($xGet): ?array {
          if (!$err = StandardError::winpct($model->wins, $model->battles)) {
            return null;
          }

          return [
            'x' => (int)ArrayHelper::getValue($model, $xGet),
            'yMin' => [100 * $err['min95ci'], 100 * $err['min99ci']],
            'yMax' => [100 * $err['max95ci'], 100 * $err['max99ci']],
          ];
        },
      ),
    ),
  ),
  'errorBarColor' => 'rgba(50, 50, 50, 0.75)',
  'errorBarLineWidth' => 1,
  'errorBarWhiskerColor' => 'rgba(50, 50, 50, 0.75)',
  'errorBarWhiskerLineWidth' => 1,
  'label' => Yii::t('app', 'Error bars'),
  'type' => 'scatterWithErrorBars',
];

$chart = [
  'data' => [
    'datasets' => array_filter(
      [
        $datasetWinPct,
        $datasetWinPctErrors,
        $datasetBattles,
      ],
    ),
  ],
  'options' => [
    'aspectRatio' => new JsExpression('16/9'),
    'animation' => [
      'duration' => 0,
    ],
    'plugins' => [
      'legend' => [
        'display' => true,
      ],
    ],
    'scales' => [
      'x' => [
        'title' => [
          'display' => false,
          'text' => $xLabel,
        ],
        'type' => 'linear',
      ],
      'y' => [
        'title' => [
          'display' => true,
          'text' => Yii::t('app', 'Win %'),
        ],
        'type' => 'linear',
        'min' => 0,
        'max' => 100,
      ],
      'y2' => [
        'position' => 'right',
        'title' => [
          'display' => true,
          'text' => Yii::t('app', 'Battles'),
        ],
        'type' => 'linear',
      ],
    ],
  ],
];

echo Html::tag(
  'div',
  Html::tag('div', '', [
    'class' => 'chart',
    'data' => [
      'chart' => $chart,
    ],
  ]),
  ['class' => 'ratio ratio-16x9'],
);
