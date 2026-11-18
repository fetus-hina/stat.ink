<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\components\helpers\DiscreteHistogram;
use app\components\helpers\StandardError;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Win % of the "higher" team for each bin of the difference, one line for each series
 *
 * Battles with no difference are excluded since there is no "higher" team.
 *
 * @var View $this
 * @var array<string, array{key: string, label: string, color: string}> $series
 * @var array<string, array<string, array{battles: int, wins: int|null}>> $rows
 * @var float $unit grid unit of the values
 * @var float $width bin width
 * @var float $overflow values greater than or equal to this are merged into one bin
 * @var int $minSamples
 * @var string $id
 * @var string $xLabel
 * @var string $yLabel
 */

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$fmt = Yii::$app->formatter;

$datasets = [];
foreach ($series as $key => $info) {
  $total = 0;
  $bins = [];
  foreach ($rows[$key] ?? [] as $value => $row) {
    if ($row['wins'] === null) {
      continue;
    }

    $start = DiscreteHistogram::binStart((float)$value, $width, $unit, $overflow);
    $binKey = sprintf('%.6f', $start);
    $bins[$binKey] ??= ['start' => $start, 'battles' => 0, 'wins' => 0];
    $bins[$binKey]['battles'] += $row['battles'];
    $bins[$binKey]['wins'] += $row['wins'];
    $total += $row['battles'];
  }
  ksort($bins, SORT_NUMERIC);

  $data = [];
  if ($total >= $minSamples) {
    foreach ($bins as $bin) {
      if ($bin['battles'] < $minSamples) {
        continue;
      }

      $err = StandardError::winpct($bin['wins'], $bin['battles']);
      $rate = 100.0 * $bin['wins'] / $bin['battles'];
      $data[] = array_merge(
        [
          'x' => round($bin['start'] + $width / 2, 6),
          'y' => $rate,
        ],
        $err
          ? [
            'yMax' => [100.0 * $err['max95ci']],
            'yMin' => [100.0 * $err['min95ci']],
          ]
          : [],
      );
    }
  }

  $datasets[] = [
    'backgroundColor' => new JsExpression($info['color']),
    'borderColor' => new JsExpression($info['color']),
    'borderWidth' => 2,
    'data' => $data,
    'errorBarColor' => new JsExpression($info['color']),
    'errorBarLineWidth' => 1,
    'errorBarWhiskerColor' => new JsExpression($info['color']),
    'errorBarWhiskerLineWidth' => 1,
    'label' => $data
      ? vsprintf('%s (n=%s)', [$info['label'], $fmt->asInteger($total)])
      : vsprintf('%s (%s)', [$info['label'], Yii::t('app', 'Not enough data is available.')]),
    'pointRadius' => 3,
    'showLine' => true,
    'type' => 'scatterWithErrorBars',
  ];
}

// reference line: 50%
$datasets[] = [
  'borderColor' => 'rgba(128, 128, 128, 0.75)',
  'borderDash' => [4, 4],
  'borderWidth' => 1,
  'data' => [
    ['x' => 0, 'y' => 50],
    ['x' => $overflow + $width, 'y' => 50],
  ],
  'label' => '50%',
  'pointRadius' => 0,
  'type' => 'line',
];

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
          'type' => 'scatter',
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
                'max' => $overflow + $width,
                'min' => 0,
                'ticks' => ['stepSize' => $width],
                'title' => [
                  'display' => true,
                  'text' => $xLabel,
                ],
                'type' => 'linear',
              ],
              'y' => [
                'suggestedMax' => 60,
                'suggestedMin' => 40,
                'ticks' => ['stepSize' => 5],
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
    <div class="small text-muted text-end text-right mb-3">
      <?= implode('<br>', array_map(
        Html::encode(...),
        [
          Yii::t('app-entire', 'Error bars: 95% confidence interval (estimated)'),
          Yii::t('app-entire', 'Bin width: {width}', [
            'width' => $fmt->asDecimal($width, 2),
          ]),
          Yii::t('app-entire', 'Values of {value} or more are merged into one bin.', [
            'value' => $fmt->asDecimal($overflow, 1),
          ]),
          Yii::t('app-entire', 'Battles with no difference are excluded.'),
          Yii::t('app-entire', 'Bins with fewer than {n} battles are not shown.', [
            'n' => $fmt->asInteger($minSamples),
          ]),
        ],
      )) . "\n" ?>
    </div>
  </div>
</div>
