<?php

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\RatioAsset;
use app\components\helpers\XPowerNormalDistribution;
use app\models\StatBigrunDistribAbstract3;
use app\models\StatEggstraWorkDistribAbstract3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var NormalDistribution|null $estimatedDistrib
 * @var NormalDistribution|null $normalDistrib
 * @var NormalDistribution|null $ruleOfThumbDistrib
 * @var StatBigrunDistribAbstract3|StatEggstraWorkDistribAbstract3|null $abstract
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

$this->registerJs("
  jQuery('.bigrun-histogram').each(
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

$makeDistributionData = function (NormalDistribution $nd) use ($abstract, $chartMax): array {
  assert($abstract);
  assert($chartMax);

  $results = [];
  $dataStep = 5;
  $makeStep = 2;
  $chartMax = (int)(ceil($chartMax / $makeStep) * $makeStep);
  for ($x = 0; $x <= $chartMax; $x += $makeStep) {
    $results[] = [
      'x' => $x,
      'y' => $nd->pdf($x) * $dataStep * $abstract->users,
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
<?php if ($estimatedDistrib && $datasetEstimatedDistrib) { ?>
<p class="mt-0 mb-3 text-muted small">
  <?= vsprintf('%s: %s %s', [
    Html::encode(Yii::t('app', 'Overall Estimates')),
    implode(' ', [
      Html::encode(Yii::t('app', 'The estimated distribution of the overall game, as estimated from the official results.')),
      Html::encode(Yii::t('app', 'Just scaled for easy contrast, the Y-axis value does not directly indicate the number of people.')),
    ]),
    sprintf('(μ=%.2f, σ=%.2f)', $estimatedDistrib->mean(), sqrt($estimatedDistrib->variance()))
  ]) . "\n" ?>
</p>
<?php } ?>
<?php if ($ruleOfThumbDistrib && $datasetRuleOfThumbDistrib) { ?>
<p class="mt-0 mb-3 text-muted small">
  <?= vsprintf('%s: %s %s', [
    Html::encode(Yii::t('app', 'Empirical Estimates')),
    implode(' ', [
      Html::encode(Yii::t('app', 'This is a wild guess based on past results and {siteName} posts.', ['siteName' => Yii::$app->name])),
      Html::encode(Yii::t('app', 'Just scaled for easy contrast, the Y-axis value does not directly indicate the number of people.')),
      Html::encode(Yii::t('app', 'The data contains a large error margins.')),
      Html::tag('b', Html::encode(Yii::t('app', 'This data is basically not informative.'))),
    ]),
    sprintf('(μ=%.2f, σ=%.2f)', $ruleOfThumbDistrib->mean(), sqrt($ruleOfThumbDistrib->variance()))
  ]) . "\n" ?>
</p>
<?php } ?>
