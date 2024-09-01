<?php

declare(strict_types=1);

use app\assets\ChartJsDataLabelsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\JqueryEasyChartjsAsset;
use app\assets\RatioAsset;
use app\components\widgets\Icon;
use app\models\BigrunMap3;
use app\models\SalmonEvent3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\StatSalmon3MapKing;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\ServerErrorHttpException;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, BigrunMap3> $bigMaps
 * @var array<int, SalmonKing3> $kings
 * @var array<int, SalmonMap3> $maps
 * @var array<int, StatSalmon3MapKing> $data
 */

JqueryEasyChartjsAsset::register($this);
ChartJsDataLabelsAsset::register($this);
RatioAsset::register($this);

$this->registerCss(
  vsprintf('.graph-container{%s}', [
    Html::cssStyleFromArray([
      'min-width' => sprintf('%dpx', 220 * (count($kings) + 1)),
    ]),
  ]),
);

$this->registerCss(
  vsprintf('.cell{%s}', [
    Html::cssStyleFromArray([
      'min-width' => '200px',
      'width' => sprintf('%f%%', 100.0 / (count($kings) + 1)),
    ]),
  ]),
);

$this->registerJs('$(".chart-data").easyChartJs();');
$this->registerJsVar(
  'chartDataLabelsFormatter',
  new JsExpression(<<<'JS'
    function (value, ctx) {
      const percentFormat = (value) => (new Intl.NumberFormat(
        document.documentElement.getAttribute('lang') || 'en-US',
        {
          style: 'percent',
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
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

$results = ArrayHelper::index(
  $data,
  'king_id',
  fn (StatSalmon3MapKing $model): int => $model->map_id
    ?? (0x100 + $model->big_map_id)
    ?? throw new ServerErrorHttpException(),
);

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-bordered table-striped table-condensed graph-container">
    <thead>
      <tr>
        <?= Html::tag(
          'th',
          Html::encode(Yii::t('app', 'Stage')),
          [
            'class' => 'text-center cell',
            'rowspan' => '2',
          ],
        ) . "\n" ?>
        <?= Html::tag(
          'th',
          Html::encode(Yii::t('app-salmon3', 'Boss Salmonids')),
          [
            'class' => 'text-center cell',
            'colspan' => count($kings),
          ],
        ) . "\n" ?>
      </tr>
      <tr>
<?php foreach ($kings as $king) { ?>
        <?= Html::tag(
          'th',
          implode(' ', [
            Icon::s3BossSalmonid($king),
            Html::tag(
              'span',
              Html::encode(Yii::t('app-salmon-boss3', $king->name)),
              ['class' => 'd-none d-md-inline'],
            ),
          ]),
          ['class' => 'text-center cell align-middle'],
        ) . "\n" ?>
<?php } ?>
    </thead>
    <tbody>
<?php foreach (array_merge($maps, $bigMaps) as $map) { ?>
      <tr>
        <?= Html::tag(
          'th',
          Html::tag(
            'div',
            implode('', [
              Html::tag(
                'div',
                match ($map::class) {
                  SalmonMap3::class => Icon::s3SalmonStage($map),
                  BigrunMap3::class => Icon::s3BigRun(),
                  default => throw new ServerErrorHttpException(),
                },
                ['style' => 'font-size:3em'],
              ),
              Html::tag(
                'div',
                Html::encode(Yii::t('app-map3', $map->short_name)),
                ['class' => 'd-md-none'],
              ),
              Html::tag(
                'div',
                Html::encode(Yii::t('app-map3', $map->name)),
                ['class' => 'd-none d-md-block'],
              ),
            ]),
          ),
          [
            'class' => 'cell align-middle text-center',
            'scope' => 'row',
          ],
        ) . "\n" ?>
<?php foreach ($kings as $king) { ?>
<?php $model = ArrayHelper::getValue($results, [
  $map instanceof SalmonMap3 ? $map->id : ($map->id + 0x100),
  $king->id,
]) ?>
        <?= Html::tag(
          'td',
          implode('', [
            Html::tag(
              'div',
              '',
              $model
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
                              $model->cleared,
                              $model->jobs - $model->cleared,
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
                          'onClick' => new JsExpression('() => {}'),
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
                            'formatter' => new JsExpression('window.chartDataLabelsFormatter'),
                          ],
                        ],
                      ],
                    ],
                  ],
                ]
                : [
                  'class' => 'ratio ratio-1x1',
                ],
            ),
            Html::tag(
              'div',
              Yii::$app->formatter->asInteger($model?->jobs ?? 0),
              ['class' => 'mt-1 small'],
            ),
          ]),
          ['class' => 'text-center cell align-middle'],
        ) . "\n" ?>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
