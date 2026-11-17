<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\components\helpers\Rank3Helper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var array<string, list<int|float|null>> $series
 * @var bool $isRanked
 * @var bool $showPerBattle
 * @var 'win-rate'|'stats'|'inked' $kind
 */

ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$color = fn (string $expr): JsExpression => new JsExpression("window.colorScheme.{$expr}");

$hasValue = fn (string $key): bool => array_filter(
  $series[$key],
  fn ($v): bool => $v !== null,
) !== [];

// [series key, label, color, y axis, extra options]
$defs = match ($kind) {
  'win-rate' => [
    ['winRate', Yii::t('app-battle', 'Winning Percentage'), $color('graph1'), 'y', []],
    ['winRate20', Yii::t('app-battle', 'Win % ({0} Battles)', [20]), $color('moving1'), 'y', []],
    ['winRate50', Yii::t('app-battle', 'Win % ({0} Battles)', [50]), $color('moving2'), 'y', []],
    ...($isRanked
      ? [
        ['rank', Yii::t('app', 'Rank'), $color('_gray.darkGray'), 'yRank', ['stepped' => 'before']],
        ['xPower', Yii::t('app', 'X Power'), $color('_accent.purple'), 'yXPower', []],
      ]
      : []),
  ],
  'stats' => [
    ['killRatio', Yii::t('app-results', 'Kill Ratio'), $color('_accent.orange'), 'y', []],
    ['avgKill', Yii::t('app-results', 'Avg Kills'), $color('_accent.red'), 'yAvg', []],
    ['avgDeath', Yii::t('app-results', 'Avg Deaths'), $color('_accent.blue'), 'yAvg', []],
    ['avgSpecial', Yii::t('app-results', 'Avg Specials'), $color('_accent.altGreen'), 'yAvg', []],
  ],
  'inked' => [
    ['avgInked', Yii::t('app-results', 'Avg Inked'), $color('_accent.green'), 'y', []],
    // Per-battle values are meaningless after thinning
    ...($showPerBattle
      ? [['inked', Yii::t('app-results', 'Turf Inked'), $color('_bg.orange'), 'y', ['fill' => 'origin']]]
      : []),
  ],
};

$datasets = [];
$axes = [];
foreach ($defs as [$key, $label, $lineColor, $axis, $extra]) {
  if (!$hasValue($key)) {
    continue;
  }

  $datasets[] = array_merge(
    [
      'label' => $label,
      'data' => $series[$key],
      'borderColor' => $lineColor,
      'backgroundColor' => $lineColor,
      'borderWidth' => 2,
      'pointRadius' => 0,
      'spanGaps' => true,
      'yAxisID' => $axis,
    ],
    $extra,
  );
  $axes[$axis] = true;
}

if (!$datasets) {
  echo Html::tag('p', Html::encode(Yii::t('app', 'No Data')), ['class' => 'text-muted']);
  return;
}

$maxRank = max([10, ...array_filter($series['rank'], fn ($v): bool => $v !== null)]);
$rankLabels = [];
for ($i = 0; $i <= $maxRank; ++$i) {
  $rankLabels[$i] = Rank3Helper::toLabel($i);
}

$scaleDefs = [
  'win-rate' => [
    'y' => [
      'position' => 'left',
      'min' => 0,
      'max' => 100,
      'ticks' => ['callback' => new JsExpression('(v) => v + "%"')],
    ],
    'yRank' => [
      'position' => 'right',
      'min' => 0,
      'grid' => ['drawOnChartArea' => false],
      'ticks' => [
        'stepSize' => 1,
        'callback' => new JsExpression(
          sprintf('(v) => (%s)[v] ?? ""', Json::encode($rankLabels)),
        ),
      ],
    ],
    'yXPower' => [
      'position' => 'right',
      'grid' => ['drawOnChartArea' => false],
    ],
  ],
  'stats' => [
    'y' => [
      'position' => 'left',
      'min' => 0,
    ],
    'yAvg' => [
      'position' => 'right',
      'min' => 0,
      'grid' => ['drawOnChartArea' => false],
    ],
  ],
  'inked' => [
    'y' => [
      'position' => 'left',
      'min' => 0,
    ],
  ],
][$kind];

$scales = ['x' => ['ticks' => ['autoSkip' => true, 'maxRotation' => 0]]];
foreach ($scaleDefs as $axis => $def) {
  if (isset($axes[$axis])) {
    $scales[$axis] = $def;
  }
}

// Tooltip: format the numbers, and show the rank by its name
$tooltipLabel = new JsExpression(
  vsprintf('(ctx) => { %s }', [
    implode(' ', [
      'const v = ctx.parsed.y;',
      'if (v === null || v === undefined) { return ctx.dataset.label + ": -"; }',
      sprintf(
        'if (ctx.dataset.yAxisID === "yRank") { return ctx.dataset.label + ": " + ((%s)[v] ?? v); }',
        Json::encode($rankLabels),
      ),
      'if (ctx.dataset.yAxisID === "y" && ctx.chart.options.scales.y.max === 100) {',
      '  return ctx.dataset.label + ": " + v.toFixed(1) + "%";',
      '}',
      'return ctx.dataset.label + ": " + (Number.isInteger(v) ? String(v) : v.toFixed(2));',
    ]),
  ]),
);

echo Html::tag(
  'div',
  Html::tag('div', '', [
    'class' => 'rule-trend-chart',
    'data' => [
      'chart' => [
        'type' => 'line',
        'data' => [
          'labels' => $series['battle'],
          'datasets' => $datasets,
        ],
        'options' => [
          'animation' => false,
          'aspectRatio' => 16 / 9,
          'interaction' => [
            'intersect' => false,
            'mode' => 'index',
          ],
          'plugins' => [
            'tooltip' => [
              'callbacks' => [
                'label' => $tooltipLabel,
              ],
            ],
          ],
          'scales' => $scales,
        ],
      ],
    ],
  ]),
  ['class' => ['ratio', 'ratio-16x9', 'mb-3']],
);

$this->registerJs('jQuery(".rule-trend-chart").easyChartJs();');
