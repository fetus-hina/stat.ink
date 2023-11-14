<?php

declare(strict_types=1);

use app\assets\JqueryEasyChartjsAsset;
use app\components\helpers\XPowerNormalDistribution;
use app\models\Rule3;
use app\models\StatXPowerDistribAbstract3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<int, StatXPowerDistribAbstract3> $abstracts
 */

JqueryEasyChartjsAsset::register($this);

$this->registerJs(vsprintf('$(%s).easyChartJs();', [
  Json::encode('.xpower-integrated-chart'),
]));

?>
<div class="row">
  <div class="col-xs-12 col-md-9 col-lg-7 mb-3">
    <?= Html::tag('div', '', [
      'class' => 'ratio ratio-16x9 xpower-integrated-chart',
      'data' => [
        'chart' => [
          'type' => 'line',
          'data' => [
            'datasets' => array_values(
              array_filter(
                array_map(
                  fn (Rule3 $rule): array => [
                    'backgroundColor' => [
                      new JsExpression(
                        vsprintf('window.colorScheme.%s', [
                          $rule->key,
                        ]),
                      ),
                    ],
                    'borderColor' => [
                      new JsExpression(
                        vsprintf('window.colorScheme.%s', [
                          $rule->key,
                        ]),
                      ),
                    ],
                    'borderWidth' => 2,
                    'data' => XPowerNormalDistribution::getDistributionFromStatXPowerDistribAbstract3(
                      abstract: $abstracts[$rule->id] ?? null,
                    ),
                    'label' => Yii::t('app-rule3', $rule->name),
                    'pointRadius' => 0,
                    'type' => 'line',
                  ],
                  $rules,
                ),
                fn (array $v): bool => (bool)count($v['data']),
              ),
            ),
          ],
          'options' => [
            'animation' => ['duration' => 0],
            'aspectRatio' => new JsExpression('16/9'),
            'layout' => ['padding' => 0],
            'legend' => ['display' => false],
            'plugins' => [
              'legend' => ['display' => true],
              'tooltip' => ['enabled' => false],
            ],
            'scales' => [
              'x' => [
                'grid' => ['offset' => false],
                'offset' => true,
                'ticks' => ['stepSize' => 200],
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
