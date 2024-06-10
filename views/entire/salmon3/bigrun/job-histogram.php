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

$makeWaveDistributionData = function (
  StatBigrunDistribJobAbstract3 $abstract,
  int $failedWave,
  string $prefix,
) use ($makeDistributionData): ?array {
  if (
    $abstract->{$prefix . '_jobs'} >= 10 &&
    $abstract->{$prefix . '_average'} !== null &&
    $abstract->{$prefix . '_stddev'} !== null &&
    $abstract->{$prefix . '_stddev'} > 0
  ) {
    $color = $failedWave > 3
      ? new JsExpression('window.colorScheme.graph1')
      : new JsExpression('window.colorScheme._gray.black');
    return [
      'backgroundColor' => [ $color ],
      'borderColor' => [ $color ],
      'borderWidth' => $failedWave > 3 ? 2 : 1,
      'data' => $makeDistributionData(
        average: $abstract->{$prefix . '_average'},
        stddev: $abstract->{$prefix . '_stddev'},
        samples: $abstract->{$prefix . '_jobs'},
        dataStep: $abstract->histogram_width,
      ),
      'label' => $failedWave > 3
        ? Yii::t('app-salmon2', 'Cleared')
        : Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
          'waveNumber' => $failedWave,
        ]),
      'pointRadius' => 0,
      'type' => 'line',
    ];
  }

  return null;
};

// $datasetNormalDistrib = [
//   'backgroundColor' => [ new JsExpression('window.colorScheme.graph1') ],
//   'borderColor' => [ new JsExpression('window.colorScheme.graph1') ],
//   'borderWidth' => 2,
//   'data' => $makeDistributionData(
//     average: $abstract->average,
//     stddev: $abstract->stddev,
//     samples: $abstract->jobs,
//     dataStep: $abstract->histogram_width,
//   ),
//   'label' => Yii::t('app', 'Normal Distribution'),
//   'pointRadius' => 0,
//   'type' => 'line',
// ];

$datasetW1FailedDistrib = $makeWaveDistributionData($abstract, 1, 'w1_failed');
$datasetW2FailedDistrib = $makeWaveDistributionData($abstract, 2, 'w2_failed');
$datasetW3FailedDistrib = $makeWaveDistributionData($abstract, 3, 'w3_failed');
$datasetClearedDistrib = $makeWaveDistributionData($abstract, 4, 'clear');

?>
<?= Html::tag('div', '', [
  'class' => 'bigrun-histogram ratio ratio-4x3',
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => array_values(
          array_filter(
            [
              $datasetW1FailedDistrib,
              $datasetW2FailedDistrib,
              $datasetW3FailedDistrib,
              $datasetClearedDistrib,
              // $datasetNormalDistrib,
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
              'stepSize' => $abstract?->histogram_width ?? 5,
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
