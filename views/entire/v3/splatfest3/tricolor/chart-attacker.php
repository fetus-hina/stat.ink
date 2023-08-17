<?php

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\components\helpers\StandardError;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var int $battles
 * @var int $wins
 */

$data = StandardError::winpct($wins, $battles);
if (!$data) {
  return;
}

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);

$this->registerJs('$(".splatfest3-chart-attacker").easyChartJs();');

$chartRangeOneSide = max(
    10,
    (int)abs(50 - ceil($data['min99ci'] * 100)),
    (int)abs(50 - ceil($data['max99ci'] * 100)),
);
$chartRangeOneSide = (int)ceil($chartRangeOneSide / 10) * 10;

$valueData = [
  'backgroundColor' => new JsExpression('window.colorScheme.graph1'),
  'borderColor' => new JsExpression('window.colorScheme.graph1'),
  'data' => [
    [
      'x' => $data['rate'] * 100,
      'xMax' => [$data['max95ci'] * 100, $data['max99ci'] * 100],
      'xMin' => [$data['min95ci'] * 100, $data['min99ci'] * 100],
      'y' => 0,
    ],
  ],
  'fill' => true,
  'label' => Yii::t('app', 'Win %'),
  'errorBarWhiskerLineWidth' => [1, 1],
  'errorBarLineWidth' => [1, 1],
  'type' => 'barWithErrorBars',
];

?>
<?= Html::tag('div', '', [
  'class' => 'splatfest3-chart-attacker mb-1',
  'style' => [
    'height' => '7em',
  ],
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => [
          $valueData,
        ],
        'labels' => [
          Yii::t('app-rule3', 'Attackers'),
        ],
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
            'title' => [
              'display' => true,
              'text' => Yii::t('app', 'Attacker Team Win Rate') . '(%)',
            ],
            'type' => 'linear',
            'ticks' => [
              'precision' => 0,
              'stepSize' => 10,
            ],
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
