<?php

declare(strict_types=1);

use MathPHP\Statistics\Correlation;
use MathPHP\Statistics\Regression\Linear as LinearRegression;
use app\components\helpers\StandardError;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[] $data
 * @var string|string[]|callable(StatWeapon3Usage|StatWeapon3UsagePerVersion): int|float|null $getX
 * @var string $xLabel
 * @var View $this
 */

$data = array_values(
  array_filter(
    $data,
    fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): bool => $model->battles > 10 && $model->avg_death > 0,
  ),
);
if (!$data) {
  return;
}

$xyPoints = array_map(
  fn (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): array => [
    'x' => ArrayHelper::getValue($model, $getX),
    'y' => 100.0 * $model->wins / $model->battles,
  ],
  $data,
);

// 相関係数
$correlationCoefficient = Correlation::r(
  ArrayHelper::getColumn($xyPoints, 'x'),
  ArrayHelper::getColumn($xyPoints, 'y'),
);

// 回帰直線
$regression = abs($correlationCoefficient) >= 0.2
  ? new LinearRegression(
    ArrayHelper::getColumn(
      $xyPoints,
      fn (array $v): array => [
        ArrayHelper::getValue($v, 'x'),
        ArrayHelper::getValue($v, 'y'),
      ],
    ),
  )
  : null;

$datasetPoints = [
  'type' => 'scatter',
  'label' => Yii::t('app', 'Win %'),
  'labels' => array_map(
    function (StatWeapon3Usage|StatWeapon3UsagePerVersion $model): string {
      $weaponName = Yii::t('app-weapon3', $model->weapon?->name ?? '?');
      $err = StandardError::winpct($model->wins, $model->battles);
      $f = Yii::$app->formatter;
      return $err
        ? vsprintf('%s (%s: %s %s)', [
          $weaponName,
          $f->asPercent($model->wins / $model->battles, 2),
          Yii::t('app', '{pct}% CI', [
            'pct' => 99,
          ]),
          Yii::t('app', '{from} - {to}', [
            'from' => $f->asPercent($err['min99ci'], 2),
            'to' => $f->asPercent($err['max99ci'], 2),
          ]),
        ])
        : $weaponName;
    },
    $data,
  ),
  'data' => $xyPoints,
  'backgroundColor' => new JsExpression('window.colorScheme.graph2'),
];

$datasetLinerRegression = null;
if ($regression) {
  $tmpXList = ArrayHelper::getColumn($xyPoints, 'x');
  $tmpMinX = min($tmpXList);
  $tmpMaxX = max($tmpXList);

  $datasetLinerRegression = [
    'type' => 'line',
    'label' => $regression->getEquation(),
    'fill' => false,
    'borderColor' => new JsExpression('window.colorScheme.graph1'),
    'borderWidth' => 1.5,
    'pointRadius' => 0,
    'data' => [
      [
        'x' => $tmpMinX - 0.01,
        'y' => $regression->evaluate($tmpMinX - 0.01),
      ],
      [
        'x' => $tmpMaxX + 0.01,
        'y' => $regression->evaluate($tmpMaxX + 0.01),
      ],
    ],
  ];
}

$chart = [
  'data' => [
    'datasets' => array_filter(
      [
        $datasetPoints,
        $datasetLinerRegression,
      ],
    ),
  ],
  'options' => [
    'aspectRatio' => new JsExpression('16/9'),
    'animation' => [
      'duration' => 0,
    ],
    'plugins' => [
      'legend' => [
        'display' => false,
      ],
      'tooltip' => [
        'callbacks' => [
          'label' => new JsExpression('ctx => ctx.dataset.labels[ctx.dataIndex]'),
        ],
      ],
    ],
    'scales' => [
      'x' => [
        'title' => [
          'display' => false,
          'text' => $xLabel,
        ],
        'type' => 'linear',
      ],
      'y' => [
        'title' => [
          'display' => true,
          'text' => Yii::t('app', 'Win %'),
        ],
        'type' => 'linear',
      ],
    ],
  ],
];

if (!$data) {
  return;
}

echo Html::tag(
  'div',
  Html::tag(
    'div',
    Html::tag(
      'div',
      implode('', [
        Html::tag('h3', Html::encode($xLabel), ['class' => 'text-center mb-1 h4']),
        Html::tag(
          'div',
          Html::tag(
            'div',
            '',
            [
              'class' => 'chart',
              'data' => [
                'chart' => $chart,
              ],
            ],
          ),
          ['class' => 'ratio ratio-16x9 mb-2'],
        ),
        Html::tag(
          'p',
          Html::encode(
            vsprintf('%s: %s', [
              Yii::t('app', 'Correlation Coefficient'),
              Yii::$app->formatter->asDecimal($correlationCoefficient, 3),
            ]),
          ),
          ['class' => 'small text-muted text-right mb-1'],
        ),
        Html::tag(
          'p',
          Html::encode(
            vsprintf('%s: %s', [
              Yii::t('app', 'Regression Line'),
              $regression ? $regression->getEquation() : Yii::t('app', 'N/A'),
            ]),
          ),
          ['class' => 'small text-muted text-right mb-1']
        ),
      ]),
      ['class' => 'panel-body pb-2'],
    ),
    ['class' => 'panel panel-default shadow-sm'],
  ),
  ['class' => 'col-xs-12 col-lg-6 mb-3'],
);
