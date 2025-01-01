<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\components\helpers\StandardError;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, Map3> $stages
 * @var array{map_id: int, battles: int, attacker_wins: int}[] $tricolorStats
 */

$fmt = Yii::$app->formatter;

$labels = [];
$dataSets = [];
$rowId = 0;

$make = fn (int $rowId, array $errInfo): array => [
  'x' => $errInfo['rate'] * 100,
  'xMax' => [$errInfo['max95ci'] * 100, $errInfo['max99ci'] * 100],
  'xMin' => [$errInfo['min95ci'] * 100, $errInfo['min99ci'] * 100],
  'y' => $rowId,
];

if (
  count($tricolorStats) > 1 &&
  $errInfo = StandardError::winpct(
    (int)array_sum(ArrayHelper::getColumn($tricolorStats, 'attacker_wins')),
    (int)array_sum(ArrayHelper::getColumn($tricolorStats, 'battles')),
  )
) {
  $labels[] = Yii::t('app', 'Total');
  $dataSets[] = $make($rowId++, $errInfo);
}

foreach ($tricolorStats as $row) {
  if (!$errInfo = StandardError::winpct((int)$row['attacker_wins'], (int)$row['battles'])) {
    continue;
  }

  $labels[] = vsprintf('%s (%s)', [
    Yii::t('app-map3', $stages[$row['map_id']]?->short_name ?? '') ?: sprintf('#%d', $row['map_id']),
    $fmt->asInteger($row['battles']),
  ]);
  $dataSets[] = $make($rowId++, $errInfo);
}

if (!$labels || !$dataSets) {
  return;
}

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);

$this->registerJs('$(".splatfest3-chart-attacker").easyChartJs();');

$min99ci = min(ArrayHelper::getColumn($dataSets, 'xMin.1'));
$max99ci = max(ArrayHelper::getColumn($dataSets, 'xMax.1'));

$chartRangeOneSide = max(
    20,
    (int)abs(50 - ceil($min99ci)),
    (int)abs(50 - ceil($max99ci)),
);
$chartRangeOneSide = (int)ceil($chartRangeOneSide / 10) * 10;

?>
<?= Html::tag('div', '', [
  'class' => 'splatfest3-chart-attacker mb-1',
  'style' => [
    'height' => sprintf('%dem', count($labels) * 2.25 + 5),
  ],
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => [[
          'backgroundColor' => new JsExpression('window.colorScheme.graph1'),
          'borderColor' => new JsExpression('window.colorScheme.graph1'),
          'data' => $dataSets,
          'errorBarLineWidth' => [1, 1],
          'errorBarWhiskerLineWidth' => [1, 1],
          'fill' => true,
          'label' => Yii::t('app', 'Win %'),
          'type' => 'barWithErrorBars',
        ]],
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
            'max' => min(100, 50 + $chartRangeOneSide),
            'min' => max(0, 50 - $chartRangeOneSide),
            'offset' => false,
            'position' => 'top',
            'ticks' => [
              'precision' => 0,
              'stepSize' => 10,
            ],
            'title' => [
              'display' => true,
              'text' => Yii::t('app', 'Attacker Team Win Rate') . ' (%)',
            ],
            'type' => 'linear',
          ],
          'y' => [
            'offset' => true,
          ],
        ],
      ],
    ],
  ],
]) . "\n" ?>
<?= Html::tag(
  'p',
  Html::encode(
    Yii::t('app', 'Error bars: 95% confidence interval (estimated) & 99% confidence interval (estimated)'),
  ),
  [
    'class' => [
      'mb-3',
      'small',
      'text-end',
      'text-muted',
      'text-right',
    ],
  ],
) . "\n" ?>
