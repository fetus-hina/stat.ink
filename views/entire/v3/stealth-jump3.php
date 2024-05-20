<?php

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsErrorBarsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\components\helpers\OgpHelper;
use app\components\helpers\StandardError;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatStealthJumpEquipment3;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var Season3 $season
 * @var StatStealthJumpEquipment3[] $data
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<int, Season3> $seasons
 * @var callable(Rule3): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 * @var float $xpAvg
 * @var float $xpStdDev
 */

$title = Yii::t('app', 'Stealth Jump Equipment Rate');
$this->title = $title . ' | ' . Yii::$app->name;

$minXPower = (int)(floor(($xpAvg - 2.0 * $xpStdDev) / 50) * 50);
$maxXPower = (int)(ceil(($xpAvg + 2.0 * $xpStdDev) / 50) * 50);

OgpHelper::default($this, title: $title);

ChartJsAsset::register($this);
ChartJsErrorBarsAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$this->registerJs('$("#stealth-jump-chart").easyChartJs();');

$datasetPlayers = [
  'backgroundColor' => [
    new JsExpression('window.colorScheme.graph2'),
  ],
  'borderColor' => [
    new JsExpression('window.colorScheme.graph2'),
  ],
  'borderWidth' => 1,
  'data' => array_map(
    fn (StatStealthJumpEquipment3 $v): array => [
      'x' => (int)$v->x_power,
      'y' => (int)$v->players,
    ],
    $data,
  ),
  'label' => Yii::t('app', 'Players'),
  'yAxisID' => 'y2',
];

$datasetEquipment = [
  'type' => 'scatter',
  'backgroundColor' => [
    new JsExpression('window.colorScheme.graph1'),
  ],
  'barPercentage' => 1.0,
  'borderColor' => [
    new JsExpression('window.colorScheme.graph1'),
  ],
  'borderWidth' => 1,
  'categoryPercentage' => 1.0,
  'data' => array_map(
    fn (StatStealthJumpEquipment3 $v): array => [
      'x' => (int)$v->x_power,
      'y' => 100 * (int)$v->equipments / (int)$v->players,
    ],
    $data,
  ),
  'label' => Yii::t('app', 'Equip %'),
];

$datasetEquipError = [
  'type' => 'scatterWithErrorBars',
  'data' => array_values(array_filter(
    array_map(
      function (StatStealthJumpEquipment3 $row): ?array {
        if (!$err = StandardError::winpct($row->equipments, $row->players)) {
          return null;
        }

        return [
          'x' => (int)$row->x_power,
          'yMax' => [100.0 * $err['max95ci']],
          'yMin' => [100.0 * $err['min95ci']],
        ];
      },
      $data,
    ),
    fn (?array $v): bool => $v !== null,
  )),
  'backgroundColor' => 'rgba(50, 50, 50, 0.75)',
  'borderColor' => 'rgba(50, 50, 50, 0.75)',
  'errorBarColor' => 'rgba(50, 50, 50, 0.75)',
  'errorBarLineWidth' => 2,
  'errorBarWhiskerColor' => 'rgba(50, 50, 50, 0.75)',
  'errorBarWhiskerLineWidth' => 2,
  'label' => Yii::t('app', 'Estimated Error') . ' (95% CI)',
];

?>
<div class="container">
  <?= Html::tag(
    'h1',
    implode(' ', [
      Icon::s3AbilityStealthJump(),
      Html::encode($title),
    ]),
  ) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <div class="mb-1">
      <?= $this->render(
        'includes/season-selector',
        compact('season', 'seasonUrl', 'seasons'),
      ) . "\n" ?>
    </div>
    <?= $this->render('weapons3/rule-tabs', compact('rule', 'rules', 'ruleUrl')) . "\n" ?>
  </div>

  <p class="mb-3">
    <?= Yii::t('app', 'Aggregated: {rules}', [
      'rules' => implode(', ', [
        Icon::s3LobbyX() . ' ' . Html::encode(Yii::t('app-lobby3', 'X Battle')),
        Html::encode(Yii::t('app', '7 players for each battle (excluded the battle uploader)')),
      ]),
    ]) . "\n" ?>
  </p>

  <div class="alert alert-danger">
    <?= Html::encode(
      vsprintf('%s: %s', [
        Yii::t('app', 'Player Distribution'),
        Yii::t('app', 'This data is based on {siteName} users and differs significantly from overall game statistics.', [
          'siteName' => Yii::$app->name,
        ]),
      ]),
    ) . "\n" ?>
  </div>

  <?= Html::tag('div', '', [
    'class' => 'ratio ratio-16x9 mb-2',
    'id' => 'stealth-jump-chart',
    'data' => [
      'chart' => [
        'type' => 'bar',
        'data' => [
          'datasets' => [
            $datasetEquipment,
            $datasetEquipError,
            $datasetPlayers,
          ],
        ],
        'options' => [
          'animation' => false,
          'aspectRatio' => new JsExpression('16/9'),
          'layout' => [
            'padding' => 0,
          ],
          'legend' => [
            'display' => false,
          ],
          'plugins' => [
            'legend' => [
              'display' => true,
              'reverse' => true,
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
              'offset' =>  true,
              'ticks' => [
                'stepSize' => 100,
              ],
              'type' => 'linear',
              'title' => [
                'display' => true,
                'text' => Yii::t('app', 'X Power'),
              ],
              'min' => $minXPower,
              'max' => $maxXPower,
            ],
            'y' => [
              'beginAtZero' => true,
              'min' => 0,
              'max' => 100,
              'position' => 'left',
              'ticks' => ['stepSize' => 10],
              'title' => [
                'display' => true,
                'text' => Yii::t('app', 'Equip %'),
              ],
            ],
            'y2' => [
              'beginAtZero' => true,
              'grid' => [
                'display' => false,
              ],
              'min' => 0,
              'position' => 'right',
              'ticks' => ['stepSize' => 500],
              'type' => 'linear',
              'title' => [
                'display' => true,
                'text' => Yii::t('app', 'Players'),
              ],
            ],
          ],
        ],
      ],
    ],
  ]) . "\n" ?>
  <div class="text-end text-right mb-3">
    <p class="small">
      <?= Html::encode(Yii::t('app', 'Error bars: 95% confidence interval (estimated)'). "\n") ?>
    </p>
    <p class="small">
      <?= Yii::t('app', 'Idea: {source}', [
        'source' => implode(', ', [
          Html::a(
            Icon::youtube() . ' わたる / wataru ch',
            'https://www.youtube.com/watch?v=pjzu_IVNXZU',
            [
              'target' => '_blank',
              'rel' => 'external nofollow noreferrer',
            ],
          ),
          Html::a(
            Icon::twitter() . ' @zounoosiri',
            'https://twitter.com/zounoosiri/status/1789158986513821810',
            [
              'target' => '_blank',
              'rel' => 'external nofollow noreferrer',
            ],
          ),
        ]),
      ]) . "\n" ?>
  </div>
</div>
