<?php

declare(strict_types=1);

use app\actions\show\v3\stats\SeasonXPowerAction;
use app\assets\ChartJsLuxonAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\models\Rule3;
use app\models\Season3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @phpstan-import-type DailyData from SeasonXPowerAction
 *
 * @var DailyData[] $dailyData
 * @var Rule3[] $rules
 * @var Season3 $season,
 * @var View $this
 */

$makeLineDataset = function (Rule3 $rule, array $dailyData): array {
  return [
    'label' => Yii::t('app-rule3', $rule->name),
    'data' => array_values(
      array_filter(
        array_map(
          function (array $current) use ($rule): ?array {
            if ($current['rule_id'] !== $rule->id) {
              return null;
            }

            return [
              'x' => $current['date'],
              'y' => $current['final'],
            ];
          },
          $dailyData,
        ),
        fn ($value): bool => is_array($value),
      ),
    ),
    'fill' => false,
    'borderColor' => new JsExpression(sprintf('window.colorScheme[%s]', Json::encode($rule->key))),
    'backgroundColor' => new JsExpression(sprintf('window.colorScheme[%s]', Json::encode($rule->key))),
    'borderWidth' => 2,
    'stepped' => 'before',
  ];
};

ChartJsLuxonAsset::register($this);
ColorSchemeAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$date = fn (string $at, string $offset = 'P0D'): string => (new DateTimeImmutable($at))
  ->setTimezone(new DateTimeZone('Etc/UTC'))
  ->sub(new DateInterval($offset))
  ->format('Y-m-d');

echo Html::tag(
  'div',
  Html::tag('div', '', [
    'id' => 'chart-daily',
    'data' => [
      'chart' => [
        'type' => 'line',
        'data' => [
          'datasets' => array_map(
            fn (Rule3 $rule) => $makeLineDataset($rule, $dailyData),
            $rules,
          ),
        ],
        'options' => [
          'animation' => ['duration' => 0],
          'aspectRatio' => 16 / 9,
          'locale' => Yii::$app->language,
          'scales' => [
            'xAxis' => [
              'locale' => Yii::$app->language,
              'max' => $date($season->end_at, 'P1D'),
              'min' => $date($season->start_at),
              'offset' => true,
              'outputCalendar' => Yii::$app->localeCalendar,
              'type' => 'time',
              'zone' => 'Etc/UTC',
            ],
          ],
        ],
      ],
    ],
  ]),
  ['class' => ['ratio', 'ratio-16x9', 'm-0', 'p-0']],
);

$this->registerJs('jQuery("#chart-daily").easyChartJs();');
