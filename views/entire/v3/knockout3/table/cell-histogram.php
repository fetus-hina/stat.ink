<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\models\Knockout3Histogram;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var Knockout3Histogram[] $data
 * @var View $this
 */

if (!$data) {
  return '';
}

ChartJsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$this->registerJs('$(".histogram").easyChartJs();');

$histogram = ArrayHelper::map(
  $data,
  fn (Knockout3Histogram $model): int => (int)$model->class_value,
  fn (Knockout3Histogram $model): int => (int)$model->count,
);

$datasetHistogram = [
  'backgroundColor' => [
    new JsExpression('window.colorScheme.graph2'),
  ],
  'barPercentage' => 1.0,
  'borderColor' => [
    new JsExpression('window.colorScheme.graph2'),
  ],
  'borderWidth' => 1,
  'categoryPercentage' => 1.0,
  'data' => array_values(
    array_map(
      fn (int $x, int $y): array => compact('x', 'y'),
      array_keys($histogram),
      array_values($histogram),
    ),
  ),
  'label' => Yii::t('app', 'Battles'),
  'type' => 'bar',
];

echo Html::tag('div', '', [
  'class' => 'histogram ratio ratio-4x3 mb-3',
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => [
          $datasetHistogram,
        ],
      ],
      'options' => [
        'animation' => [
          'duration' => 0,
        ],
        'aspectRatio' => 4 / 3, // 16 / 10,
        'plugins' => [
          'legend' => [
            'display' => false,
            'reverse' => true,
          ],
          'tooltip' => [
            'enabled' => false,
          ],
        ],
        'scales' => [
          'x' => [
            'grid' => [
               'offset' => false,
            ],
            'offset' => false,
            'title' => [
              'display' => true,
              'text' => Yii::t('app', 'Seconds'),
            ],
            'type' => 'linear',
            'ticks' => [
              'precision' => 0,
              'stepSize' => 60,
            ],
            'min' => 0,
          ],
          'y' => [
            'min' => 0,
            'title' => [
              'display' => false,
              'text' => Yii::t('app', 'Battles'),
            ],
            'type' => 'linear',
            'display' => true,
          ],
        ],
      ],
    ],
  ],
]);
