<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, int> $histogram
 * @var int $chartStep
 * @var string $label
 */

$datasetHistogram = [
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
      fn (int $x, int $y): array => compact('x', 'y'),
      array_keys($histogram),
      array_values($histogram),
    ),
  ),
  'label' => $label . ': ' . Yii::t('app', 'Battles'),
  'type' => 'bar',
];

?>
<?= Html::tag('div', '', [
  'class' => 'event3-histogram ratio ratio-4x3 mb-3',
  'style' => [
    'max-width' => '480px',
  ],
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => [
          $datasetHistogram,
        ],
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
            'title' => [
              'display' => true,
              'text' => Yii::t('app', 'Challenge Power'),
            ],
            'type' => 'linear',
            'ticks' => [
              'precision' => 0,
              'stepSize' => $chartStep,
            ],
          ],
          'y' => [
            'min' => 0,
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
