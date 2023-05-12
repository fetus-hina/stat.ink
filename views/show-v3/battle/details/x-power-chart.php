<?php

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\components\helpers\TypeHelper;
use app\models\Battle3;
use app\models\Season3;
use yii\db\Expression as DbExpr;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 */

return [
  'label' => Yii::t('app', 'X Power'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->x_power_before === null && $model->x_power_after === null) {
      return null;
    }

    if ($model->period === null) {
      return null;
    }

    $lobby = $model->lobby;
    if ($lobby?->key !== 'xmatch') {
      return null;
    }

    $season = Season3::find()
      ->andWhere([
        '@>',
        '{{%season3}}.[[term]]',
        new DbExpr(sprintf('TO_TIMESTAMP(%d)', $model->period * 7200)),
      ])
      ->cache(86400)
      ->limit(1)
      ->one();
    if (!$season) {
      return null;
    }

    $xPowerList = ArrayHelper::getColumn(
      Battle3::find()
        ->andWhere(['and',
          [
            '{{%battle3}}.[[is_deleted]]' => false,
            '{{%battle3}}.[[lobby_id]]' => $lobby->id,
            '{{%battle3}}.[[rule_id]]' => $model->rule_id,
            '{{%battle3}}.[[user_id]]' => $model->user_id,
          ],
          ['not', ['{{%battle3}}.[[end_at]]' => null]],
          ['not', ['{{%battle3}}.[[period]]' => null]],
          ['<=', '{{%battle3}}.[[end_at]]', $model->end_at],
          ['between', '{{%battle3}}.[[period]]',
            (int)floor(strtotime($season->start_at) / 7200),
            (int)floor(strtotime($season->end_at) / 7200) - 1,
          ],
          'COALESCE({{%battle3}}.[[x_power_after]], {{%battle3}}.[[x_power_before]]) IS NOT NULL',
        ])
        ->orderBy(['{{%battle3}}.[[end_at]]' => SORT_DESC])
        ->limit(500)
        ->select([
          'x_power' => 'COALESCE({{%battle3}}.[[x_power_after]], {{%battle3}}.[[x_power_before]])',
        ])
        ->asArray()
        ->all(),
      fn (array $v): ?float => TypeHelper::floatOrNull($v['x_power'] ?? null),
    );
    if (count($xPowerList) < 2) {
      return null;
    }

    // 古い順に並べかえる
    $xPowerList = array_values(array_reverse($xPowerList));

    $id = 'x-power-chart';

    ChartJsAsset::register($this);
    ColorSchemeAsset::register($this);
    JqueryEasyChartjsAsset::register($this);
    RatioAsset::register($this);

    $configJson = Json::encode([
      'data' => [
        'labels' => [
          Yii::t('app', 'X Power'),
        ],
        'datasets' => [
          [
            'backgroundColor' => [ new JsExpression('window.colorScheme.graph1') ],
            'borderColor' => [ new JsExpression('window.colorScheme.graph1') ],
            'borderWidth' => 2,
            'fill' => false,
            'label' => Yii::t('app', 'X Power'),
            'pointRadius' => 0,
            'type' => 'line',
            'data' => array_map(
              fn (int $x, ?float $y) => compact('x', 'y'),
              range(-1 * count($xPowerList) + 1, 0),
              $xPowerList,
            ),
          ],
        ],
      ],
      'options' => [
        'animation' => [
          'duration' => 0,
        ],
        'aspectRatio' => 16 / 9,
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
            'max' => 0,
            'offset' => true,
            'title' => [
              'display' => false,
            ],
            'type' => 'linear',
          ],
          'y' => [
            'title' => [
              'display' => false,
            ],
            'type' => 'linear',
          ],
        ],
      ],
    ]);

    $this->registerJs("$('#{$id}').easyChartJs();");

    return Html::tag(
      'div',
      implode('', [
        Html::tag(
          'div',
          '',
          [
            'class' => 'mb-2 p-0 w-100 ratio ratio-16x9',
            'id' => $id,
            'data' => [
              'chart' => $configJson,
            ],
          ],
        ),
        Html::tag(
          'p',
          Html::a(
            Html::encode(Yii::t('app', 'Season')),
            ['show-v3/stats-season-x-power',
              'screen_name' => $model->user->screen_name,
              'season' => $season->id,
            ],
          ),
          ['class' => 'small text-right m-0'],
        ),
      ]),
      ['style' => ['max-width' => '400px']],
    );
  },
];
