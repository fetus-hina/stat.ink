<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use MathPHP\Statistics\Correlation;
use MathPHP\Statistics\Regression\Linear as LinearRegression;
use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\RatioAsset;
use app\components\helpers\Color;
use app\components\helpers\StandardError;
use app\models\StatInkColor3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatInkColor3[] $models
 * @var View $this
 */

if (!$models) {
  return;
}

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
RatioAsset::register($this);

$pointData = array_filter(
  ArrayHelper::getColumn(
    $models,
    function (StatInkColor3 $model): ?array {
      $battles = $model->battles;
      if (!$battles) {
        return null;
      }

      $c1 = $model->color1;
      $c2 = $model->color2;
      $y1 = Color::getYUVFromRGB(hexdec(substr($c1, 0, 2)), hexdec(substr($c1, 2, 2)), hexdec(substr($c1, 4, 2)))[0];
      $y2 = Color::getYUVFromRGB(hexdec(substr($c2, 0, 2)), hexdec(substr($c2, 2, 2)), hexdec(substr($c2, 4, 2)))[0];

      $err = StandardError::winpct($model->wins, $battles);
      if (!$err) {
        return [
          'x' => $y2 - $y1,
          'y' => 100 * $model->wins / $battles,
        ];
      }

      return [
        'x' => $y2 - $y1,
        'y' => 100 * $model->wins / $battles,
        'yMin' => [100 * $err['min95ci'], 100 * $err['min99ci']],
        'yMax' => [100 * $err['max95ci'], 100 * $err['max99ci']],
      ];
    },
  ),
  fn (?array $v): bool => $v !== null,
);

// 相関係数
$correlationCoefficient = Correlation::r(
  ArrayHelper::getColumn($pointData, 'x'),
  ArrayHelper::getColumn($pointData, 'y'),
);

// 回帰直線
$regression = abs($correlationCoefficient) >= 0.2
  ? new LinearRegression(
    ArrayHelper::getColumn(
      $pointData,
      fn (array $v): array => [
        ArrayHelper::getValue($v, 'x'),
        ArrayHelper::getValue($v, 'y'),
      ],
    ),
  )
  : null;

$minX = $regression ? min(ArrayHelper::getColumn($pointData, 'x')) : null;
$maxX = $regression ? max(ArrayHelper::getColumn($pointData, 'x')) : null;

$chartData = [
  'data' => [
    'datasets' => array_filter(
      [
        [
          'type' => 'scatter',
          'label' => Yii::t('app', 'Win %'),
          'data' => $pointData,
          'pointBackgroundColor' => ArrayHelper::getColumn(
            $models,
            fn (StatInkColor3 $model): string => sprintf('#%s', substr($model->color1, 0, 6)),
          ),
          'pointRadius' => 6,
        ],
        [
          'type' => 'scatterWithErrorBars',
          'label' => Yii::t('app', 'Win %'),
          'data' => $pointData,
          'errorBarColor' => ArrayHelper::getColumn(
            $models,
            fn (StatInkColor3 $model): string => sprintf('#%s', substr($model->color1, 0, 6)),
          ),
          'errorBarLineWidth' => 2,
          'errorBarWhiskerLineWidth' => 2,
          'errorBarWhiskerColor' => ArrayHelper::getColumn(
            $models,
            fn (StatInkColor3 $model): string => sprintf('#%s', substr($model->color1, 0, 6)),
          ),
        ],
        $regression
          ? [
            'type' => 'line',
            'label' => $regression->getEquation(),
            'fill' => false,
            'borderColor' => '#f5a101',
            'borderWidth' => 1.5,
            'pointRadius' => 0,
            'data' => [
              [
                'x' => $minX - 0.05,
                'y' => $regression->evaluate($minX - 0.05),
              ],
              [
                'x' => $maxX + 0.05,
                'y' => $regression->evaluate($maxX + 0.05),
              ],
            ],
          ]
          : null,
        [
          'type' => 'line',
          'label' => '50%',
          'fill' => false,
          'borderColor' => '#333',
          'borderWidth' => 1.5,
          'data' => [
            [
              'x' => -0.1,
              'y' => 50,
            ],
            [
              'x' => 1.1,
              'y' => 50,
            ],
          ],
        ],
      ],
      fn (?array $v): bool => $v !== null,
    ),
  ],
  'options' => [
    'aspectRatio' => 16 / 9,
    'animation' => [
      'duration' => 0,
    ],
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
        'max' => 0.6,
        'min' => 0,
        'title' => [
          'display' => true,
          'text' => Yii::t('app', 'Luminance Difference'),
        ],
        'type' => 'linear',
      ],
      'y' => [
        'max' => 56,
        'min' => 48,
        'title' => [
          'display' => true,
          'text' => Yii::t('app', 'Win %'),
        ],
        'type' => 'linear',
      ],
    ],
  ],
];

$this->registerJs('
  jQuery("#ink-color-chart").each(
    function () {
      const elem = this;
      const config = JSON.parse(this.getAttribute("data-config"));
      const canvas = elem.appendChild(document.createElement("canvas"));
      new window.Chart(canvas.getContext("2d"), config);
    }
  );
');

?>
<div class="ratio ratio-16x9">
  <?= Html::tag('div', '', [
    'id' => 'ink-color-chart',
    'data' => [
      'config' => $chartData,
    ],
  ]) . "\n" ?>
</div>
<div class="mb-3">
  <?= Html::tag(
    'p',
    Html::encode(
      Yii::t('app', 'Error bars: 95% confidence interval (estimated) & 99% confidence interval (estimated)'),
    ),
    ['class' => 'mb-1 small text-right'],
  ) . "\n" ?>
  <?= Html::tag(
    'p',
    vsprintf('%s: %s', [
      Html::encode(Yii::t('app', 'Correlation Coefficient')),
      Html::encode(Yii::$app->formatter->asDecimal($correlationCoefficient, 3)),
    ]),
    ['class' => 'mb-1 small text-right'],
  ) . "\n" ?>
  <?= $regression
    ? Html::tag(
      'p',
      vsprintf('%s: %s', [
        Html::encode(Yii::t('app', 'Regression Line')),
        Html::encode($regression->getEquation()),
      ]),
      ['class' => 'mb-1 small text-right'],
    ) . "\n"
    : ''
  ?>
</div>
