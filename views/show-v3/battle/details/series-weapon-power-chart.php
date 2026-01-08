<?php

/**
 * @copyright Copyright (C) 2025-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
  'label' => Yii::t('app', 'Series Weapon Power'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->series_weapon_power_before === null && $model->series_weapon_power_after === null) {
      return null;
    }

    if ($model->weapon_id === null) {
      return null;
    }

    $lobby = $model->lobby;
    if ($lobby?->key !== 'bankara_challenge') {
      return null;
    }

    $powerList = ArrayHelper::getColumn(
      Battle3::find()
        ->andWhere(['and',
          [
            '{{%battle3}}.[[is_deleted]]' => false,
            '{{%battle3}}.[[lobby_id]]' => $lobby->id,
            '{{%battle3}}.[[user_id]]' => $model->user_id,
            '{{%battle3}}.[[weapon_id]]' => $model->weapon_id,
          ],
          ['not', ['{{%battle3}}.[[end_at]]' => null]],
          ['<=', '{{%battle3}}.[[end_at]]', $model->end_at],
          ['>=', '{{%battle3}}.[[end_at]]', '2025-06-12T01:00:00+00:00'],
          'COALESCE({{%battle3}}.[[series_weapon_power_after]], {{%battle3}}.[[series_weapon_power_before]]) IS NOT NULL',
        ])
        ->orderBy([
          '{{%battle3}}.[[end_at]]' => SORT_DESC,
          '{{%battle3}}.[[id]]' => SORT_DESC,
        ])
        ->limit(500)
        ->select([
          'series_weapon_power' => 'COALESCE({{%battle3}}.[[series_weapon_power_after]], {{%battle3}}.[[series_weapon_power_before]])',
        ])
        ->asArray()
        ->all(),
      fn (array $v): ?float => TypeHelper::floatOrNull($v['series_weapon_power'] ?? null),
    );
    if (count($powerList) < 2) {
      return null;
    }

    // 古い順に並べかえる
    $powerList = array_values(array_reverse($powerList));

    $id = 'series-power-chart';

    ChartJsAsset::register($this);
    ColorSchemeAsset::register($this);
    JqueryEasyChartjsAsset::register($this);
    RatioAsset::register($this);

    $configJson = Json::encode([
      'data' => [
        'labels' => [
          Yii::t('app', 'Series Weapon Power'),
        ],
        'datasets' => [
          [
            'backgroundColor' => [ new JsExpression('window.colorScheme.graph1') ],
            'borderColor' => [ new JsExpression('window.colorScheme.graph1') ],
            'borderWidth' => 2,
            'fill' => false,
            'label' => Yii::t('app', 'Series Weapon Power'),
            'pointRadius' => 0,
            'type' => 'line',
            'data' => array_map(
              fn (int $x, ?float $y) => [
                'x' => $x,
                // For visibility, values smaller than 0.1 are considered invalid.
                // The Series Power can theoretically take values of 0.0 or negative values.
                // Taking 0.0 is about as likely as obtaining a value around 4000.
                // It's hard to imagine such people continuing to play until they get 0.0,
                // and the probability that they are stat.ink users is even lower.
                // If someone like that appears, it will be necessary to devise a method such as
                // determining the threshold based on surrounding values.
                //
                // https://github.com/fetus-hina/stat.ink/issues/1665
                'y' => (float)$y < 0.1 ? null : (float)$y,
              ],
              range(-1 * count($powerList) + 1, 0),
              $powerList,
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
      ]),
      ['style' => ['max-width' => '400px']],
    );
  },
];
