<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\components\helpers\StandardError;
use app\models\StatSalmon3Salmometer;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, StatSalmon3Salmometer> $data
 * @var int $totalCleared
 * @var int $totalSamples
 */

if (!$totalSamples || !$data) {
  return;
}

$makeData = fn (int $index, int $jobs, int $cleared): array => $jobs > 0
  ? (($err = StandardError::winpct($cleared, $jobs))
    ? [
      'x' => $err['rate'] * 100,
      'xMax' => [$err['max95ci'] * 100, $err['max99ci'] * 100],
      'xMin' => [$err['min95ci'] * 100, $err['min99ci'] * 100],
      'y' => $index,
    ]
    : [
      'x' => $cleared / $jobs * 100,
      'y' => $index,
    ]
  )
  : null;

$dataList = [];
$labels = [];

$dataList[] = $makeData(0, $totalSamples, $totalCleared); // total
$labels[] = Yii::t('app', 'Total');

foreach (range(0, 5) as $i) {
  if ($tmp = $makeData($i + 1, (int)$data[$i]?->jobs, (int)$data[$i]?->cleared)) {
    $dataList[] = $tmp;
    $labels[] = sprintf('%d / %d', $i, 5);
  }
}

$valueData = [
  'backgroundColor' => new JsExpression('window.colorScheme.graph1'),
  'borderColor' => new JsExpression('window.colorScheme.graph1'),
  'data' => $dataList,
  'fill' => true,
  'label' => Yii::t('app-salmon2', 'Clear %'),
  'errorBarWhiskerLineWidth' => [1, 1],
  'errorBarLineWidth' => [1, 1],
  'type' => 'barWithErrorBars',
];

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);

$this->registerJs('$(".chart").easyChartJs();');

echo Html::tag('div', '', [
  'class' => 'chart mb-1',
  'style' => [
    'height' => sprintf('%dem', count($dataList) * 2.25 + 5),
  ],
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => [
          $valueData,
        ],
        'labels' => $labels,
      ],
      'options' => [
        'animation' => [
          'duration' => 0,
        ],
        'maintainAspectRatio' => false,
        'indexAxis' => 'y',
        'plugins' => [
          'legend' => [
            'display' => false,
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
              'text' => Yii::t('app-salmon2', 'Clear %'),
            ],
            'type' => 'linear',
            'ticks' => [
              'precision' => 0,
              'stepSize' => 10,
            ],
            'position' => 'top',
          ],
          'y' => [
            'offset' => true,
          ],
        ],
      ],
    ],
  ],
]);
