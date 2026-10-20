<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\models\Salmon3UserStatsGoldenEgg;
use app\models\Salmon3UserStatsGoldenEggIndividualHistogram;
use app\models\Salmon3UserStatsGoldenEggTeamHistogram;
use app\models\SalmonMap3;
use app\models\User;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var (Salmon3UserStatsGoldenEggTeamHistogram|Salmon3UserStatsGoldenEggIndividualHistogram)[] $data
 * @var Salmon3UserStatsGoldenEgg $abstract
 * @var View $this
 * @var string $title
 */

$params = null;
if ($abstract->shifts >= 10 && count($data) >= 5) {
  ChartJsAsset::register($this);
  ColorSchemeAsset::register($this);
  JqueryEasyChartjsAsset::register($this);
  RatioAsset::register($this);

  [$average, $stddev, $width] = match (get_class($data[0] ?? throw new TypeError())) {
    Salmon3UserStatsGoldenEggTeamHistogram::class => [
      $abstract->average_team,
      $abstract->stddev_team,
      $abstract->histogram_width_team,
    ],
    Salmon3UserStatsGoldenEggIndividualHistogram::class => [
      $abstract->average_individual,
      $abstract->stddev_individual,
      $abstract->histogram_width_individual,
    ],
    default => throw new LogicException(),
  };
  if ($average !== null && $stddev !== null && $width !== null) {
    $params = [
      'average' => $average,
      'stddev' => $stddev,
      'width' => $width,
    ];
  }
  $this->registerJs('$(".histogram").easyChartJs();');
}

?>
<div class="col-12 col-xs-12 col-md-6 mb-3">
  <?= Html::tag('h4', $title, ['class' => 'mt-0 mb-3 text-center']) . "\n" ?>
<?php if ($abstract->shifts < 10 || count($data) < 5 || $params === null) { ?>
  <?= Html::tag(
    'p',
    Html::encode(Yii::t('app', 'Not enough data is available.')),
    ['class' => 'mt-0 mb-3 text-center text-muted'],
  ) . "\n" ?>
<?php } else { ?>
  <?= Html::tag('div', '', [
    'class' => 'histogram ratio ratio-4x3 mb-3',
    'style' => [
      'max-width' => '420px',
    ],
    'data' => [
      'chart' => [
        'data' => [
          'datasets' => [
            // normal distribution
            [
              'backgroundColor' => [
                new JsExpression('window.colorScheme.graph1'),
              ],
              'borderColor' => [
                new JsExpression('window.colorScheme.graph1'),
              ],
              'borderWidth' => 2,
              'data' => (function (NormalDistribution $dist, int $records, int $histogramWidth): array {
                $results = [];
                $makeStep = 1;
                $chartMin = 0;
                // $chartMin = max(
                //   0,
                //   (int)(floor($dist->mean() - 3 * sqrt($dist->variance())) / $makeStep) * $makeStep,
                // );
                $chartMax = (int)(ceil($dist->mean() + 3 * sqrt($dist->variance())) / $makeStep) * $makeStep;
                for ($x = $chartMin; $x <= $chartMax; $x += $makeStep) {
                  $results[] = [
                    'x' => $x,
                    'y' => $dist->pdf($x) * $histogramWidth * $records,
                  ];
                }
                return $results;
              })(
                new NormalDistribution(
                  $params['average'],
                  $params['stddev'],
                ),
                $abstract->shifts,
                $params['width'],
              ),
              'label' => Yii::t('app-statistics', 'Normal Distribution'),
              'pointRadius' => 0,
              'type' => 'line',
            ],
            // histogram
            [
              'backgroundColor' => [
                new JsExpression('window.colorScheme.graph2'),
              ],
              'barPercentage' => 1.0,
              'borderColor' => [
                new JsExpression('window.colorScheme.graph2'),
              ],
              'borderWidth' => 1,
              'categoryPercentage' => 1.0,
              'data' => array_values(
                array_map(
                  fn (Salmon3UserStatsGoldenEggTeamHistogram|Salmon3UserStatsGoldenEggIndividualHistogram $row): array => [
                    'x' => $row->class_value,
                    'y' => $row->count,
                  ],
                  $data,
                ),
              ),
              'label' => Yii::t('app', 'Battles'),
              'type' => 'bar',
            ],
          ],
        ],
        'options' => [
          'animation' => [
            'duration' => 0,
          ],
          'aspectRatio' => 4 / 3, // 16 / 10,
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
              'grid' => ['offset' => false],
              'min' => 0,
              'offset' => true,
              'ticks' => [
                'precision' => 0,
                'stepSize' => $params['width'],
              ],
              'title' => ['display' => false],
              'type' => 'linear',
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
<?php } ?>
</div>
