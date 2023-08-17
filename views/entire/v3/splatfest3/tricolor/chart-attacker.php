<?php

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
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

$this->registerJs("
  jQuery('.splatfest3-chart-attacker').each(
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
            'min' => 0,
            'max' => 100,
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
