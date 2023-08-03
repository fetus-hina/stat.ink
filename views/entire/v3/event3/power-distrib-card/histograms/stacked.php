<?php

declare(strict_types=1);

use app\models\Event3StatsPower;
use app\models\Event3StatsPowerPeriodHistogram;
use app\models\EventPeriod3;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var Event3StatsPower $abstract
 * @var Event3StatsPowerPeriodHistogram[] $histogram
 * @var EventPeriod3[] $periods
 * @var View $this
 */

$colors = [
  new JsExpression('window.colorScheme._accent.red'),
  new JsExpression('window.colorScheme._accent.yellow'),
  new JsExpression('window.colorScheme._accent.green'),
  new JsExpression('window.colorScheme._accent.sky'),
];

$datasets = [];
foreach ($periods as $i => $period) {
  $thisHistogram = array_filter(
    $histogram,
    fn (Event3StatsPowerPeriodHistogram $it) => $it->period_id === $period->id,
  );

  if ($thisHistogram) {
    $datasets[] = [
      'backgroundColor' => [
        $colors[$i % count($colors)],
      ],
      'barPercentage' => 1.0,
      'borderColor' => [
        $colors[$i % count($colors)],
      ],
      'borderWidth' => 1,
      'categoryPercentage' => 1.0,
      'data' => array_values(
        array_map(
          fn (Event3StatsPowerPeriodHistogram $model): array => [
            'x' => $model->class_value,
            'y' => $model->battles,
          ],
          $thisHistogram,
        ),
      ),
      'label' => mb_chr(0x2460 + $i, 'UTF-8'),
      'type' => 'bar',
    ];
  }
}

if (!$datasets) {
  return;
}

?>
<?= Html::tag('div', '', [
  'class' => 'event3-histogram ratio ratio-4x3 mb-3',
  'style' => [
    'max-width' => '480px',
  ],
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => $datasets,
      ],
      'options' => [
        'animation' => [
          'duration' => 0,
        ],
        'aspectRatio' => 4 / 3, // 16 / 10,
        'plugins' => [
          'legend' => [
            'display' => true,
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
            'offset' => true,
            'stacked' => true,
            'title' => [
              'display' => true,
              'text' => Yii::t('app', 'Challenge Power'),
            ],
            'type' => 'linear',
            'ticks' => [
              'precision' => 0,
              'stepSize' => 100,
            ],
          ],
          'y' => [
            'min' => 0,
            'stacked' => true,
            'title' => [
              'display' => true,
              'text' => Yii::t('app', 'Battles'),
            ],
            'type' => 'linear',
          ],
        ],
      ],
    ],
  ],
]) . "\n" ?>
