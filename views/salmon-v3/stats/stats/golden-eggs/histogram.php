<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\models\Salmon3UserStatsGoldenEgg;
use app\models\Salmon3UserStatsGoldenEggIndividualHistogram;
use app\models\Salmon3UserStatsGoldenEggTeamHistogram;
use app\models\SalmonMap3;
use app\models\User;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var (Salmon3UserStatsGoldenEggTeamHistogram|Salmon3UserStatsGoldenEggIndividualHistogram)[] $data
 * @var Salmon3UserStatsGoldenEgg $abstract
 * @var View $this
 * @var string $title
 */

if ($abstract->shifts >= 10 && count($data) >= 5) {
  ChartJsAsset::register($this);
  ColorSchemeAsset::register($this);
  JqueryEasyChartjsAsset::register($this);
  RatioAsset::register($this);

  $this->registerJs('$(".histogram").easyChartJs();');
}

?>
<div class="col-12 col-xs-12 col-md-6 mb-3">
  <?= Html::tag('h4', $title, ['class' => 'mt-0 mb-3 text-center']) . "\n" ?>
<?php if ($abstract->shifts < 10 || count($data) < 5) { ?>
  <?= Html::tag(
    'p',
    Html::encode(Yii::t('app', 'Not enough data is available.')),
    ['class' => 'mt-0 mb-3 text-center text-muted'],
  ) . "\n" ?>
<?php } else { ?>
  <?= Html::tag('div', '', [
    'class' => 'histogram ratio ratio-4x3 mb-3',
    'style' => [
      'max-width' => '420px',
    ],
    'data' => [
      'chart' => [
        'data' => [
          'datasets' => [
            [
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
                  fn (Salmon3UserStatsGoldenEggTeamHistogram|Salmon3UserStatsGoldenEggIndividualHistogram $row): array => [
                    'x' => $row->class_value,
                    'y' => $row->count,
                  ],
                  $data,
                ),
              ),
              'label' => Yii::t('app', 'Battles'),
              'type' => 'bar',
            ],
          ],
        ],
        'options' => [
          'animation' => [
            'duration' => 0,
          ],
          'aspectRatio' => 4 / 3, // 16 / 10,
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
              'grid' => ['offset' => false],
              'min' => 0,
              'offset' => true,
              'ticks' => [
                'precision' => 0,
                // 'stepSize' => 10,
              ],
              'title' => ['display' => false],
              'type' => 'linear',
            ],
            'y' => [
              'min' => 0,
              'title' => [
                'display' => true,
                'text' => Yii::t('app-salmon2', 'Jobs'),
              ],
              'type' => 'linear',
            ],
          ],
        ],
      ],
    ],
  ]) . "\n" ?>
<?php } ?>
</div>
