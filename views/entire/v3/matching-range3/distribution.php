<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\components\helpers\DiscreteHistogram;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Frequency polygons of the discrete distributions, one line for each series
 *
 * @var View $this
 * @var array<string, array{key: string, label: string, color: string}> $series
 * @var array<string, array<string, int>> $rows series key => (value => count)
 * @var float $unit grid unit of the values
 * @var int $minSamples
 * @var string $id
 * @var string $xLabel
 * @var string $yLabel
 */

ChartJsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$fmt = Yii::$app->formatter;

$toPairs = fn (array $values): array => array_map(
  fn (string $value, int $count): array => [(float)$value, $count],
  array_map('strval', array_keys($values)),
  array_values($values),
);

// Bin width: Scott's rule for the pooled samples, so that the series share the same bins
$pooled = DiscreteHistogram::summarize(
  array_merge([], ...array_values(array_map($toPairs, $rows))),
);
$width = DiscreteHistogram::scottWidth($pooled['n'] ?? 0, $pooled['stddev'] ?? null, $unit);

/** @var array<string, array{n: int, bins: array<string, int>}> $histograms */
$histograms = [];
$minBin = null;
$maxBin = null;
foreach ($series as $key => $info) {
  $values = $rows[$key] ?? [];
  $n = array_sum($values);
  $bins = [];
  if ($n >= $minSamples) {
    foreach ($values as $value => $count) {
      $start = DiscreteHistogram::binStart((float)$value, $width, $unit);
      $binKey = sprintf('%.6f', $start);
      $bins[$binKey] = ($bins[$binKey] ?? 0) + $count;
      $minBin = $minBin === null ? $start : min($minBin, $start);
      $maxBin = $maxBin === null ? $start : max($maxBin, $start);
    }
  }

  $histograms[$key] = ['n' => $n, 'bins' => $bins];
}

$datasets = [];
foreach ($series as $key => $info) {
  $n = $histograms[$key]['n'];
  $bins = $histograms[$key]['bins'];
  $data = [];
  if ($bins && $minBin !== null && $maxBin !== null) {
    // fill empty bins with zero to draw the polygon continuously
    $steps = (int)round(($maxBin - $minBin) / $width);
    for ($i = 0; $i <= $steps; ++$i) {
      $start = round($minBin + $i * $width, 6);
      $data[] = [
        'x' => round($start + $width / 2, 6),
        'y' => 100.0 * ($bins[sprintf('%.6f', $start)] ?? 0) / $n,
      ];
    }
  }

  $datasets[] = [
    'backgroundColor' => new JsExpression($info['color']),
    'borderColor' => new JsExpression($info['color']),
    'borderWidth' => 2,
    'data' => $data,
    'label' => $data
      ? vsprintf('%s (n=%s)', [$info['label'], $fmt->asInteger($n)])
      : vsprintf('%s (%s)', [$info['label'], Yii::t('app', 'Not enough data is available.')]),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}

$this->registerJs(vsprintf('$(%s).easyChartJs();', [
  Json::encode('#' . $id),
]));

?>
<div class="row">
  <div class="col-xs-12 col-md-10 col-lg-8">
    <?= Html::tag('div', '', [
      'class' => 'ratio ratio-16x9 mb-1',
      'id' => $id,
      'data' => [
        'chart' => [
          'type' => 'line',
          'data' => [
            'datasets' => $datasets,
          ],
          'options' => [
            'animation' => false,
            'aspectRatio' => new JsExpression('16/9'),
            'layout' => ['padding' => 0],
            'plugins' => [
              'legend' => ['display' => true],
              'tooltip' => ['enabled' => false],
            ],
            'scales' => [
              'x' => [
                'title' => [
                  'display' => true,
                  'text' => $xLabel,
                ],
                'type' => 'linear',
              ],
              'y' => [
                'beginAtZero' => true,
                'title' => [
                  'display' => true,
                  'text' => $yLabel,
                ],
              ],
            ],
          ],
        ],
      ],
    ]) . "\n" ?>
    <p class="small text-muted text-end text-right mb-3">
      <?= Html::encode(
        Yii::t('app-entire', 'Bin width: {width}', [
          'width' => $fmt->asDecimal($width, 3),
        ]),
      ) . "\n" ?>
    </p>
  </div>
</div>
