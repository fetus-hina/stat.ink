<?php

declare(strict_types=1);

use MathPHP\Statistics\Correlation;
use MathPHP\Statistics\Regression\Linear as LinearRegression;
use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\RatioAsset;
use app\components\helpers\Color;
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
  
      $rate1 = $model->wins / $battles;
      $rate2 = 1.0 - $rate1;
  
      // ref. http://lfics81.techblog.jp/archives/2982884.html
      $stderr = sqrt($battles / ($battles - 1.5) * $rate1 * $rate2) / sqrt($battles);
      // $err95ci = $stderr * 1.96;
      $err99ci = $stderr * 2.58;

      return [
        'x' => $y2 - $y1,
        'y' => 100 * $rate1,
        'yMin' => 100 * ($rate1 - $err99ci),
        'yMax' => 100 * ($rate1 + $err99ci),
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
$regression = abs($correlationCoefficient) >= 0.3
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
          'errorBarColor' => 'rgba(50, 50, 50, 0.75)',
          'errorBarLineWidth' => 1,
          'errorBarWhiskerLineWidth' => 1,
          'errorBarWhiskerColor' => 'rgba(50, 50, 50, 0.75)',
        ],
        $regression
          ? [
            'type' => 'line',
            'label' => $regression->getEquation(),
            'fill' => false,
            'borderColor' => '#f5a101',
            'borderWidth' => 1.5,
            'borderDash' => [10, 5],
            'data' => [
              [
                'x' => -0.1,
                'y' => $regression->evaluate(-0.1),
              ],
              [
                'x' => 1.1,
                'y' => $regression->evaluate(1.1),
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
<?= Html::tag(
  'p',
  vsprintf('%s: %s', [
    Html::encode(Yii::t('app', 'Correlation Coefficient')),
    Html::encode(Yii::$app->formatter->asDecimal($correlationCoefficient, 3)),
  ]),
  ['class' => 'mb-2 small text-right'],
) . "\n" ?>
<?= $regression
  ? Html::tag(
    'p',
    vsprintf('%s: %s', [
      Html::encode(Yii::t('app', 'Regression Line')),
      Html::encode($regression->getEquation()),
    ]),
    ['class' => 'mb-2 small text-right'],
  ) . "\n"
  : ''
?>
