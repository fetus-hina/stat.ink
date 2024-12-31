<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use app\models\StatWeapon3XUsagePerVersion;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[]|StatWeapon3XUsage[]|StatWeapon3XUsagePerVersion[] $data
 * @var View $this
 */

if (!$data) {
  echo Html::tag(
    'div',
    Html::encode(Yii::t('yii', 'No results found.')),
    ['class' => 'mb-3'],
  );
  return;
}

$data = ArrayHelper::sort(
  $data,
  fn (
    StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $a,
    StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $b,
  ): float => ($b->wins / $b->battles) <=> ($a->wins / $a->battles)
    ?: $a->battles <=> $b->battles,
);

$valueData = [
  'backgroundColor' => new JsExpression('window.colorScheme.graph1'),
  'borderColor' => new JsExpression('window.colorScheme.graph1'),
  'data' => array_values(
    array_map(
      function (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $model, int $i): array {
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
      $data,
      range(0, count($data) - 1),
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
  'class' => 'chart mb-1',
  'style' => [
    'height' => sprintf('%dem', count($data) * 2.25 + 5),
  ],
  'data' => [
    'chart' => [
      'data' => [
        'datasets' => [
          $valueData,
        ],
        'labels' => array_map(
          fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $model): string => vsprintf('%s (%s)', [
            Yii::t('app-weapon3', $model->weapon->name),
            Yii::$app->formatter->asInteger($model->battles),
          ]),
          $data,
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
