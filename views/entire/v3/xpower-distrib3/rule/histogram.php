<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\XPowerNormalDistribution;
use app\models\StatXPowerDistribAbstract3;
use app\models\StatXPowerDistribHistogram3;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var StatXPowerDistribAbstract3|null $abstract
 * @var StatXPowerDistribHistogram3[] $data
 * @var View $this
 */

if (!$data) {
  return;
}

$normalDistribData = Yii::$app->cache->getOrSet(
  [__FILE__, __LINE__, $abstract?->attributes],
  fn () => XPowerNormalDistribution::getDistributionFromStatXPowerDistribAbstract3(
    abstract: $abstract,
  ),
  86400,
);

$datasetHistogram = [
  'backgroundColor' => [new JsExpression('window.colorScheme.graph2')],
  'barPercentage' => 1.0,
  'borderColor' => [new JsExpression('window.colorScheme.graph2')],
  'borderWidth' => 1,
  'categoryPercentage' => 1.0,
  'data' => array_map(
    fn (StatXPowerDistribHistogram3 $v): array => [
      'x' => (int)$v->class_value,
      'y' => (int)$v->users,
    ],
    $data,
  ),
  'label' => Yii::t('app', 'Users'),
];

$datasetNormalDistrib = $normalDistribData
  ? [
    'backgroundColor' => [new JsExpression('window.colorScheme.graph1')],
    'borderColor' => [new JsExpression('window.colorScheme.graph1')],
    'borderWidth' =>  2,
    'data' => $normalDistribData,
    'label' => Yii::t('app', 'Normal Distribution'),
    'pointRadius' => 0,
    'type' => 'line'
  ]
  : null;

?>
<div class="row">
  <div class="col-xs-12 col-md-9 col-lg-7 mb-3">
    <?= Html::tag('div', '', [
      'class' => 'ratio ratio-16x9 xpower-distrib-chart',
      'data' => [
        'chart' => [
          'type' => 'bar',
          'data' => [
            'datasets' => array_values(
              array_filter([
                $datasetNormalDistrib,
                $datasetHistogram,
              ]),
            ),
          ],
          'options' => [
            'aspectRatio' => new JsExpression('16/9'),
            'layout' => ['padding' => 0],
            'legend' => ['display' => false],
            'plugins' => [
              'legend' => [
                'display' => true,
                'reverse' => true,
              ],
              'tooltip' => ['enabled' => false],
            ],
            'scales' => [
              'x' => [
                'grid' => ['offset' => false],
                'offset' =>  true,
                'ticks' => [
                  'stepSize' => $abstract?->histogram_width ?? 100,
                ],
                'title' => [
                  'display' => true,
                  'text' => Yii::t('app', 'X Power'),
                ],
                'type' => 'linear',
              ],
              'y' => [
                'beginAtZero' => true,
                'title' => [
                  'display' => true,
                  'text' => Yii::t('app', 'Users'),
                ],
              ],
            ],
          ],
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
