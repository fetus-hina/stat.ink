<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\components\helpers\StandardError;
use app\models\Splatfest3StatsWeapon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var Splatfest3StatsWeapon $models
 * @var View $this
 */

if (!$models) {
  echo Html::tag(
    'div',
    Html::encode(Yii::t('yii', 'No results found.')),
    ['class' => 'mb-3'],
  );
  return;
}

$models = ArrayHelper::sort(
  $models,
  fn (Splatfest3StatsWeapon $a, Splatfest3StatsWeapon $b): float =>
    ($b->wins / $b->battles) <=> ($a->wins / $a->battles)
      ?: $a->battles <=> $b->battles,
);

$samples = array_sum(
  array_map(
    fn (Splatfest3StatsWeapon $model): int => $model->battles,
    $models,
  ),
);

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);

$this->registerJs('$(".splatfest3-chart-win-rate").easyChartJs();');

$valueData = [
  'backgroundColor' => new JsExpression('window.colorScheme.graph1'),
  'borderColor' => new JsExpression('window.colorScheme.graph1'),
  'data' => array_values(
    array_map(
      function (Splatfest3StatsWeapon $model, int $i): array {
        $data = StandardError::winpct($model->wins, $model->battles);
        return $data
          ? [
            'x' => $data['rate'] * 100,
            'xMax' => [$data['max95ci'] * 100, $data['max99ci'] * 100],
            'xMin' => [$data['min95ci'] * 100, $data['min99ci'] * 100],
            'y' => $i,
          ]
          : [
            'x' => $model->wins / $model->battles * 100,
            'y' => $i,
          ];
      },
      $models,
      range(0, count($models) - 1),
    ),
  ),
  'fill' => true,
  'label' => Yii::t('app', 'Win %'),
  'errorBarWhiskerLineWidth' => [1, 1],
  'errorBarLineWidth' => [1, 1],
  'type' => 'barWithErrorBars',
];

?>
<p class="mb-1">
  <?= Html::encode(
    vsprintf('%s: %s', [
      Yii::t('app', 'Samples'),
      Yii::$app->formatter->asInteger($samples),
    ]),
  ) . "\n" ?>
</p>
<?= Html::tag('div', '', [
  'class' => 'splatfest3-chart-win-rate mb-1',
  'style' => [
    'height' => sprintf('%dem', count($models) * 2.25 + 5),
  ],
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => [
          $valueData,
        ],
        'labels' => array_map(
          fn (Splatfest3StatsWeapon $model): string => vsprintf('%s (%s)', [
            Yii::t('app-weapon3', $model->weapon->name),
            Yii::$app->formatter->asInteger($model->battles),
          ]),
          $models,
        ),
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
            'max' => 100,
            'min' => 0,
            'offset' => false,
            'title' => [
              'display' => true,
              'text' => Yii::t('app', 'Win %'),
            ],
            'type' => 'linear',
            'ticks' => [
              'precision' => 0,
              'stepSize' => 10,
            ],
            'position' => 'top',
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
