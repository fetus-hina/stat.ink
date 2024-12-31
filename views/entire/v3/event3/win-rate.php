<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\components\helpers\StandardError;
use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var Event3StatsWeapon[]|Event3StatsSpecial[] $models
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
  fn (Event3StatsWeapon|Event3StatsSpecial $a, Event3StatsWeapon|Event3StatsSpecial $b): float =>
    ($b->wins / $b->battles) <=> ($a->wins / $a->battles)
    ?: $a->battles <=> $b->battles,
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
      function (Event3StatsWeapon|Event3StatsSpecial $model, int $i): array {
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
          fn (Event3StatsWeapon|Event3StatsSpecial $model): string => vsprintf('%s (%s)', [
            match ($model::class) {
              Event3StatsWeapon::class => Yii::t('app-weapon3', $model->weapon->name),
              Event3StatsSpecial::class => Yii::t('app-special3', $model->special->name),
            },
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
