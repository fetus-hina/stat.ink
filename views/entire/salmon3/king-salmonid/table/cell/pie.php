<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsDataLabelsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var bool $labelText
 * @var int $cleared
 * @var int $jobs
 */

ColorSchemeAsset::register($this);
ChartJsDataLabelsAsset::register($this);
JqueryEasyChartjsAsset::register($this);
RatioAsset::register($this);

$this->registerJs('$(".chart-data").easyChartJs();');

if ($labelText) {
  $this->registerJsVar(
    'chartDataLabelsFormatterWithLabel',
    new JsExpression(<<<'JS'
      function (value, ctx) {
        const percentFormat = (value) => (new Intl.NumberFormat(
          document.documentElement.getAttribute('lang') || 'en-US',
          {
            style: 'percent',
            minimumFractionDigits: 1,
            maximumFractionDigits: 1
          }
        )).format(value);
        if (value === null || value === undefined) {
          return '';
        }
        const sum = ctx.dataset.data.reduce(
          (acc, cur) => typeof (cur) === 'number' ? Number(acc) + Number(cur) : Number(acc),
          0
        );
        if (sum < 1) {
          return '';
        }
        const label = ctx.chart.legend.legendItems[ctx.dataIndex].text;
        return label + '\n' + percentFormat(value / sum);
      }
    JS),
  );
} else {
  $this->registerJsVar(
    'chartDataLabelsFormatter',
    new JsExpression(<<<'JS'
      function (value, ctx) {
        const percentFormat = (value) => (new Intl.NumberFormat(
          document.documentElement.getAttribute('lang') || 'en-US',
          {
            style: 'percent',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
          }
        )).format(value);
        if (value === null || value === undefined) {
          return '';
        }
        const sum = ctx.dataset.data.reduce(
          (acc, cur) => typeof (cur) === 'number' ? Number(acc) + Number(cur) : Number(acc),
          0
        );
        if (sum < 1) {
          return '';
        }
        return percentFormat(value / sum);
      }
    JS),
  );
}

echo Html::tag(
  'div',
  '',
  $jobs > 0
    ? [
      'class' => 'ratio ratio-1x1 chart-data',
      'data' => [
        'chart' => [
          'plugins' => [
            new JsExpression('window.ChartDataLabels'),
          ],   
          'type' => 'pie',
          'data' => [
            'labels' => [
              Yii::t('app-salmon2', 'Cleared'),
              Yii::t('app-salmon2', 'Failed'),
            ],
            'datasets' => [
              [
                'data' => [
                  $cleared,
                  $jobs - $cleared,
                ],
                'backgroundColor' => [
                  new JsExpression('window.colorScheme.win'),
                  new JsExpression('window.colorScheme.lose'),
                ],
              ],
            ],
          ],
          'options' => [
            'animation' => [
              'duration' => 0,
            ],
            'aspectRatio' => 1 / 1,
            'legend' => [
              // do nothing, to disable label-click
              'onClick' => new JsExpression('()=>{}'),
            ],
            'plugins' => [
              'legend' => [
                'display' => false,
              ],
              'tooltip' => [
                'enabled' => true,
              ],
              'datalabels' => [
                'backgroundColor' => new JsExpression(
                  implode('', [
                    '(ctx)=>{',
                    'const v=ctx.dataset.data[ctx.dataIndex];',
                    'return (typeof v==="number")?"rgba(255,255,255,0.8)":null;',
                    '}',
                  ]),
                ),
                'font' => [
                  'weight' => 'bold',
                ],
                'formatter' => new JsExpression(
                  $labelText
                    ? 'window.chartDataLabelsFormatterWithLabel'
                    : 'window.chartDataLabelsFormatter',
                ),
              ],
            ],
          ],
        ],
      ],
    ]
    : [
      'class' => 'ratio ratio-1x1',
    ],
);
