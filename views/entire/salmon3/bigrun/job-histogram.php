<?php

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\models\StatBigrunDistribJobAbstract3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var StatBigrunDistribJobAbstract3|null $abstract
 * @var View $this
 * @var array<int, int> $histogram
 * @var int|null $chartMax
 */

if (
    !$histogram ||
    !$abstract ||
    count($histogram) < 5 ||
    $abstract->average === null ||
    $abstract->stddev === null
) {
  return;
}

ChartJsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$this->registerJs('$(".bigrun-histogram").easyChartJs();');

$datasetHistogram = [
  'backgroundColor' => [ new JsExpression('window.colorScheme.graph2') ],
  'barPercentage' => 1.0,
  'borderColor' => [ new JsExpression('window.colorScheme.graph2') ],
  'borderWidth' => 1,
  'categoryPercentage' => 1.0,
  'data' => array_values(
    array_map(
      fn (int $x, int $y): array => compact('x', 'y'),
      array_keys($histogram),
      array_values($histogram),
    ),
  ),
  'label' => Yii::t('app-salmon2', 'Jobs'),
  'type' => 'bar',
];

$makeDistributionData = function (float $average, float $stddev, int $samples, int $dataStep): array {
  $results = [];
  $makeStep = 2;
  $nd = new NormalDistribution($average, $stddev);
  $chartMin = max(0, (int)floor(($average - $stddev * 3) / $makeStep) * $makeStep);
  $chartMax = (int)ceil(($average + $stddev * 3) / $makeStep) * $makeStep;
  for ($x = $chartMin; $x <= $chartMax; $x += $makeStep) {
    $results[] = [
      'x' => $x,
      'y' => $nd->pdf($x) * $dataStep * $samples,
    ];
  }
  return $results;
};

$datasetNormalDistrib = [
  'backgroundColor' => [ new JsExpression('window.colorScheme.graph1') ],
  'borderColor' => [ new JsExpression('window.colorScheme.graph1') ],
  'borderWidth' => 2,
  'data' => $makeDistributionData(
    average: $abstract->average,
    stddev: $abstract->stddev,
    samples: $abstract->jobs,
    dataStep: $abstract->histogram_width,
  ),
  'label' => Yii::t('app', 'Normal Distribution'),
  'pointRadius' => 0,
  'type' => 'line',
];

$datasetClearedDistrib = null;
if (
    $abstract->clear_jobs >= 10 &&
    $abstract->clear_average !== null &&
    $abstract->clear_stddev !== null &&
    $abstract->clear_stddev > 0
) {
  $datasetClearedDistrib = [
    'backgroundColor' => [ new JsExpression('window.colorScheme._accent.sky') ],
    'borderColor' => [ new JsExpression('window.colorScheme._accent.sky') ],
    'borderWidth' => 2,
    'data' => $makeDistributionData(
      average: $abstract->clear_average,
      stddev: $abstract->clear_stddev,
      samples: $abstract->clear_jobs,
      dataStep: $abstract->histogram_width,
    ),
    'label' => vsprintf('%s (%s)', [
      Yii::t('app', 'Normal Distribution'),
      Yii::t('app-salmon2', 'Cleared'),
    ]),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}

?>
<?= Html::tag('div', '', [
  'class' => 'bigrun-histogram ratio ratio-4x3',
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => array_values(
          array_filter(
            [
              $datasetClearedDistrib,
              $datasetNormalDistrib,
              $datasetHistogram,
            ],
          ),
        ),
      ],
      'options' => [
        'animation' => ['duration' => 0],
        'aspectRatio' => 4 / 3, // 16 / 10,
        'plugins' => [
          'legend' => [
            'display' => true,
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
            'min' => 0,
            'offset' => true,
            'title' => [
              'display' => true,
              'text' => Yii::t('app-salmon2', 'Golden Eggs'),
            ],
            'type' => 'linear',
            'ticks' => [
              'precision' => 0,
              'stepSize' => 5,
            ],
          ],
          'y' => [
            'min' => 0,
            'title' => [
              'display' => true,
              'text' => Yii::t('app-salmon2', 'Jobs'),
            ],
            'type' => 'linear',
          ],
        ],
      ],
    ],
  ],
]) . "\n" ?>
