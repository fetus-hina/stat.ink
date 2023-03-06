<?php

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\RatioAsset;
use app\components\helpers\XPowerNormalDistribution;
use app\models\StatBigrunDistribAbstract3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var StatBigrunDistribAbstract3|null $abstract
 * @var View $this
 * @var array<int, float> $estimatedDistrib
 * @var array<int, float> $normalDistrib
 * @var array<int, int> $histogram
 * @var float|null $estimatedAverage
 * @var float|null $estimatedStddev
 */

if (!$histogram) {
  return;
}

ChartJsAsset::register($this);
ColorSchemeAsset::register($this);
RatioAsset::register($this);

$this->registerJs("
  jQuery('#bigrun-histogram').each(
    function () {
      function looseJsonParse (obj) {
        return Function('\"use strict\";return (' + obj + ')')();
      }

      const elem = this;
      const config = looseJsonParse(this.getAttribute('data-chart'));
      const canvas = elem.appendChild(document.createElement('canvas'));
      new window.Chart(canvas.getContext('2d'), config);
    }
  );
");

$datasetHistogram = [
  'backgroundColor' => [ new JsExpression('window.colorScheme.graph2') ],
  'borderColor' => [ new JsExpression('window.colorScheme.graph2') ],
  'borderWidth' => 1,
  'data' => array_values(
    array_map(
      fn (int $x, int $y): array => compact('x', 'y'),
      array_keys($histogram),
      array_values($histogram),
    ),
  ),
  'label' => Yii::t('app', 'Users'),
  'type' => 'bar',
];

$datasetNormalDistrib = null;
if ($normalDistrib) {
  $datasetNormalDistrib = [
    'backgroundColor' => [ new JsExpression('window.colorScheme.graph1') ],
    'borderColor' => [ new JsExpression('window.colorScheme.graph1') ],
    'borderWidth' => 2,
    'data' => array_values(
      array_map(
        fn (int $x, float $y): array => compact('x', 'y'),
        array_keys($normalDistrib),
        array_values($normalDistrib),
      ),
    ),
    'label' => Yii::t('app', 'Normal Distribution'),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}

$datasetEstimatedDistrib = null;
if ($estimatedDistrib) {
  $datasetEstimatedDistrib = [
    'backgroundColor' => [ new JsExpression('window.colorScheme.moving1') ],
    'borderColor' => [ new JsExpression('window.colorScheme.moving1') ],
    'borderWidth' => 2,
    'data' => array_values(
      array_map(
        fn (int $x, float $y): array => compact('x', 'y'),
        array_keys($estimatedDistrib),
        array_values($estimatedDistrib),
      ),
    ),
    'label' => Yii::t('app', 'Overall Estimates'),
    'pointRadius' => 0,
    'type' => 'line',
  ];
}

?>
<div class="row">
  <div class="col-xs-12 col-md-9 col-lg-7 mb-3">
    <?= Html::tag('div', '', [
      'id' => 'bigrun-histogram',
      'class' => 'ratio ratio-4x3',
      'data' => [
        'chart' => [
          'data' => [
            'datasets' => array_values(
              array_filter(
                [
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
                  'stepSize' => 5,
                ],
              ],
              'y' => [
                'min' => 0,
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
<?php if ($datasetEstimatedDistrib) { ?>
<p class="mt-0 mb-3 text-muted small">
  <?= vsprintf('%s: %s', [
    Html::encode(Yii::t('app', 'Overall Estimates')),
    implode(' ', [
      Html::encode(Yii::t('app', 'The estimated distribution of the overall game, as estimated from the official results.')),
      Html::encode(Yii::t('app', 'Just scaled for easy contrast, the Y-axis value does not directly indicate the number of people.')),
    ]),
  ]) . "\n" ?>
<?php if ($estimatedAverage && $estimatedStddev) { ?>
  <?= vsprintf('(μ=%.2f, σ=%.2f)', [
    $estimatedAverage,
    $estimatedStddev,
  ]) . "\n" ?>
<?php } ?>
<p>
<?php } ?>
