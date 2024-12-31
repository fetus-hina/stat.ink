<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\RatioAsset;
use app\models\StatEggstraWorkDistribUserAbstract3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var NormalDistribution|null $estimatedDistrib
 * @var NormalDistribution|null $normalDistrib
 * @var NormalDistribution|null $ruleOfThumbDistrib
 * @var StatEggstraWorkDistribUserAbstract3|null $abstract
 * @var View $this
 * @var array<int, int> $histogram
 * @var int|null $chartMax
 */

if (!$histogram) {
  return;
}

ChartJsAsset::register($this);
ColorSchemeAsset::register($this);
RatioAsset::register($this);

$totalUsers = array_sum(array_values($histogram));
if ($totalUsers < 1) {
  return;
}

$binWidth = $abstract?->histogram_width ?? 10;
$keyMax = max(array_keys($histogram));
$totalHistogram = [];
for ($x = $binWidth / 2; $x <= $keyMax; $x += $binWidth) {
  $totalHistogram[] = [
    'x' => $x,
    'y' => array_sum(
      array_filter(
        $histogram,
        fn (int $v): bool => $v <= $x,
        ARRAY_FILTER_USE_KEY,
      ),
    ) / $totalUsers,
  ];
}

$datasetHistogram = [
  'backgroundColor' => [ new JsExpression('window.colorScheme.graph2') ],
  'barPercentage' => 1.0,
  'borderColor' => [ new JsExpression('window.colorScheme.graph2') ],
  'borderWidth' => 1,
  'categoryPercentage' => 1.0,
  'data' => $totalHistogram,
  'label' => Yii::t('app', 'Users'),
  'type' => 'bar',
];

$makeDistributionData = function (NormalDistribution $nd) use ($abstract, $chartMax): array {
  assert($abstract);
  assert($chartMax);

  $results = [];
  $makeStep = 2;
  $chartMax = (int)(ceil($chartMax / $makeStep) * $makeStep);
  for ($x = 0; $x <= $chartMax; $x += $makeStep) {
    $results[] = [
      'x' => $x,
      'y' => $nd->cdf($x),
    ];
  }
  return $results;
};

$datasetNormalDistrib = null;
if ($normalDistrib && $abstract && $chartMax > 0) {
  $datasetNormalDistrib = [
    'backgroundColor' => [ new JsExpression('window.colorScheme.graph1') ],
    'borderColor' => [ new JsExpression('window.colorScheme.graph1') ],
    'borderWidth' => 2,
    'data' => $makeDistributionData($normalDistrib),
    'label' => Yii::t('app', 'Normal Distribution'),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}

$datasetEstimatedDistrib = null;
if ($estimatedDistrib && $abstract && $chartMax > 0) {
  $datasetEstimatedDistrib = [
    'backgroundColor' => [ new JsExpression('window.colorScheme.moving1') ],
    'borderColor' => [ new JsExpression('window.colorScheme.moving1') ],
    'borderWidth' => 2,
    'data' => $makeDistributionData($estimatedDistrib),
    'label' => Yii::t('app', 'Overall Estimates'),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}

$datasetRuleOfThumbDistrib = null;
if (!$datasetEstimatedDistrib && $ruleOfThumbDistrib && $abstract && $chartMax > 0) {
  $datasetRuleOfThumbDistrib = [
    'backgroundColor' => [ new JsExpression('window.colorScheme.moving1') ],
    'borderColor' => [ new JsExpression('window.colorScheme.moving1') ],
    'borderWidth' => 2,
    'borderDash' => [5, 5],
    'data' => $makeDistributionData($ruleOfThumbDistrib),
    'label' => Yii::t('app', 'Empirical Estimates'),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}


$dataset95pct = null;
$dataset80pct = null;
$dataset50pct = null;
if ($chartMax > 0) {
  $dataset95pct = [
    'animation' => [
      'duration' => 0,
    ],
    'backgroundColor' => [ new JsExpression('window.colorScheme._accent.red') ],
    'borderColor' => [ new JsExpression('window.colorScheme._accent.red') ],
    'borderWidth' => 1,
    'data' => [
      ['x' => 0, 'y' => 0.95],
      ['x' => $chartMax, 'y' => 0.95],
    ],
    'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 5]),
    'pointRadius' => 0,
    'type' => 'line',
  ];

  $dataset80pct = [
    'animation' => [
      'duration' => 0,
    ],
    'backgroundColor' => [ new JsExpression('window.colorScheme._accent.red') ],
    'borderColor' => [ new JsExpression('window.colorScheme._accent.red') ],
    'borderWidth' => 1,
    'data' => [
      ['x' => 0, 'y' => 0.8],
      ['x' => $chartMax, 'y' => 0.8],
    ],
    'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 20]),
    'pointRadius' => 0,
    'type' => 'line',
  ];

  $dataset50pct = [
    'animation' => [
      'duration' => 0,
    ],
    'backgroundColor' => [ new JsExpression('window.colorScheme._accent.red') ],
    'borderColor' => [ new JsExpression('window.colorScheme._accent.red') ],
    'borderWidth' => 1,
    'data' => [
      ['x' => 0, 'y' => 0.5],
      ['x' => $chartMax, 'y' => 0.5],
    ],
    'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 50]),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}

?>
<div class="row">
  <div class="col-xs-12 col-md-9 col-lg-7 mb-3">
    <?= Html::tag('div', '', [
      'class' => 'bigrun-histogram ratio ratio-4x3',
      'data' => [
        'chart' => [
          'data' => [
            'datasets' => array_values(
              array_filter(
                [
                  $dataset50pct,
                  $dataset80pct,
                  $dataset95pct,
                  $datasetRuleOfThumbDistrib,
                  $datasetEstimatedDistrib,
                  $datasetNormalDistrib,
                  $datasetHistogram,
                ],
              ),
            ),
          ],
          'options' => [
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
                'max' => 1.0,
                'ticks' => [
                  'format' => [
                    'style' => 'percent',
                  ],
                ],
                'title' => [
                  'display' => true,
                  'text' => Yii::t('app', 'Users'),
                ],
                'type' => 'linear',
              ],
            ],
          ],
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
