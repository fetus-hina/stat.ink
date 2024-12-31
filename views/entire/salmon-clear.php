<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\ChartJsDataLabelsAsset;
use app\assets\ColorSchemeAsset;
use app\assets\PatternomalyAsset;
use app\assets\Spl2WeaponAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;

$title = Yii::t('app-salmon2', 'Clear rate of Salmon Run');
$this->title = Yii::$app->name . ' | ' . $title;

$fmt = Yii::$app->formatter;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

ChartJsAsset::register($this);
ChartJsDataLabelsAsset::register($this);
ColorSchemeAsset::register($this);
PatternomalyAsset::register($this);
$wAsset = Spl2WeaponAsset::register($this);

$this->registerCss(<<<'EOF'
  .wStatsRoot {
    display: flex;
    flex-wrap: wrap;
    margin-right: -10px;
  }
  .wStats {
    flex: 0 0 8em;
    margin-bottom: 20px;
    margin-right: 10px;
    text-align: center;
  }
  .wStats img {
    max-width: 100%;
    height: auto;
  }
  .weapon-rate {
    width: 8em;
  }
EOF);

$this->registerJs(<<<'EOF'
  (function () {
    var percentFormat = function (value) {
      return (new Intl.NumberFormat(
        document.documentElement.getAttribute('lang') || 'en-US',
        {
          style: 'percent',
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        }
      )).format(value);
    };

    var pat = window.pattern;
    var defBgColors = window.colorScheme._bg;
    var bgColors = [
      pat.draw('disc', defBgColors.yellow),
      pat.draw('zigzag-vertical', defBgColors.blue),
      pat.draw('ring', defBgColors.red),
      pat.draw('line', defBgColors.orange),
      pat.draw('line-vertical', defBgColors.purple),
      pat.draw('dot', defBgColors.green),
    ];
    $('.clear-rate').each(function(_, elem) {
      var canvas = elem.appendChild(document.createElement('canvas'));
      var labels = JSON.parse(elem.getAttribute('data-labels'));
      var values = JSON.parse(elem.getAttribute('data-values'));
      var ctx = canvas.getContext('2d');
      var chart = new Chart(ctx, {
        plugins: [
          window.ChartDataLabels,
        ],
        type: 'pie',
        data: {
          datasets: [
            {
              data: [
                values.failed_w1,
                values.failed_w2,
                values.failed_w3,
                null,
                null,
                values.cleared,
              ],
              backgroundColor: bgColors,
            },
            {
              data: [
                null,
                null,
                null,
                values.wiped,
                values.timed,
                values.cleared,
              ],
              backgroundColor: bgColors,
            },
          ],
          labels: [
            labels.failed_w1,
            labels.failed_w2,
            labels.failed_w3,
            labels.wiped,
            labels.timed,
            labels.cleared,
          ],
        },
        options: {
          aspectRatio: 1,
          legend: {
            onClick: function(event, legendItem) {
              // do nothing, to disable label-click
            },
          },
          plugins: {
            datalabels: {
              backgroundColor: function (ctx) {
                var value = ctx.dataset.data[ctx.dataIndex];
                return (typeof value === 'number')
                  ? 'rgba(255, 255, 255, 0.5)'
                  : null;
              },
              font: {
                weight: 'bold',
              },
              formatter: function (value, ctx) {
                if (value === null || value === undefined) {
                  return '';
                }

                var sum = ctx.dataset.data.reduce(
                  function (acc, cur) {
                    return (typeof(cur) === 'number')
                      ? Number(acc) + Number(cur)
                      : Number(acc);
                  },
                  0
                );
                if (sum < 1) {
                  return '';
                }

                var label = ctx.chart.legend.legendItems[ctx.dataIndex].text;
                return label + "\n" + percentFormat(value / sum);
              }
            },
          },
        },
      });
    });
  })();
EOF);

$this->registerJs(<<<'EOF'
  (function () {
    var percentFormat = function (value) {
      return (new Intl.NumberFormat(
        document.documentElement.getAttribute('lang') || 'en-US',
        {
          style: 'percent',
          minimumFractionDigits: 1,
          maximumFractionDigits: 1,
        }
      )).format(value);
    };

    var pat = window.pattern;
    var defBgColors = window.colorScheme._bg;
    var bgColors = [
      pat.draw('cross', defBgColors.red),
      pat.draw('dot', defBgColors.green),
    ];
    $('.weapon-rate').each(function(_, elem) {
      var canvas = elem.appendChild(document.createElement('canvas'));
      var labels = JSON.parse(elem.getAttribute('data-labels'));
      var values = JSON.parse(elem.getAttribute('data-values'));
      var ctx = canvas.getContext('2d');
      var chart = new Chart(ctx, {
        plugins: [
          window.ChartDataLabels,
        ],
        type: 'pie',
        data: {
          datasets: [
            {
              data: [
                values.failed,
                values.cleared,
              ],
              backgroundColor: bgColors,
            },
          ],
          labels: [
            labels.failed,
            labels.cleared,
          ],
        },
        options: {
          aspectRatio: 1,
          plugins: {
            legend: {
              display: false,
            },
            datalabels: {
              backgroundColor: function (ctx) {
                var value = ctx.dataset.data[ctx.dataIndex];
                return (typeof value === 'number')
                  ? 'rgba(255, 255, 255, 0.5)'
                  : null;
              },
              font: {
                weight: 'bold',
              },
              formatter: function (value, ctx) {
                if (value === null || value === undefined) {
                  return '';
                }

                var sum = ctx.dataset.data.reduce(
                  function (acc, cur) {
                    return (typeof(cur) === 'number')
                      ? Number(acc) + Number(cur)
                      : Number(acc);
                  },
                  0
                );
                if (sum < 1) {
                  return '';
                }

                var label = ctx.chart.legend.legendItems[ctx.dataIndex].text;
                return label + "\n" + percentFormat(value / sum);
              }
            },
          },
        },
      });
    });
  })();
EOF);

$wLabels = [
  'cleared' => Yii::t('app-salmon2', 'Cleared'),
  'failed' => Yii::t('app-salmon2', 'Failed'),
];

?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
<?php foreach ($models as $model) { ?>
  <?= Html::tag(
    'h2',
    Html::encode(Yii::t('app-salmon-map2', $model->stage->name)),
    ['id' => $model->stage->key]
  ) . "\n" ?>
  <div class="row">
    <div class="col-12 col-xs-12 col-sm-5 col-lg-4">
      <?= Html::tag('div', '', [
        'class' => ['chart', 'clear-rate'],
        'data' => [
          'values' => [
            'cleared' => (int)$model->cleared,
            'failed_w1' => (int)$model->fail_wave1,
            'failed_w2' => (int)$model->fail_wave2,
            'failed_w3' => (int)$model->fail_wave3,
            'wiped' => (int)$model->fail_wiped,
            'timed' => (int)$model->fail_timed,
          ],
          'labels' => [
            'cleared' => Yii::t('app-salmon2', 'Cleared'),
            'failed_w1' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                'waveNumber' => 1,
            ]),
            'failed_w2' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                'waveNumber' => 2,
            ]),
            'failed_w3' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                'waveNumber' => 3,
            ]),
            'wiped' => Yii::t('app-salmon2', 'Wipe out'),
            'timed' => Yii::t('app-salmon2', 'Time is up'),
          ],
        ],
      ]) . "\n" ?>
      <p class="small text-muted font-italic text-center">
        n = <?= $fmt->asInteger($model->plays) . "\n" ?>
      </p>
    </div>
    <div class="col-12 col-xs-12 col-sm-7 col-lg-8">
      <div class="table-responsive table-responsive-force">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th></th>
              <th><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
              <th><?= Html::encode(Yii::t('app-salmon2', 'Golden')) ?> *1</th>
              <th><?= Html::encode(Yii::t('app-salmon2', 'Pwr Eggs')) ?> *1</th>
              <th><?= Html::encode(Yii::t('app-salmon2', 'Deaths')) ?> *1</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row"><?= Html::encode(Yii::t('app', 'Average')) ?></th>
              <td><?= $fmt->asDecimal($model->avg_clear_waves, 4) ?></td>
              <td><?= $fmt->asDecimal($model->avg_golden_eggs, 3) ?></td>
              <td><?= $fmt->asDecimal($model->avg_power_eggs, 3) ?></td>
              <td><?= $fmt->asDecimal($model->avg_deaths, 3) ?></td>
            </tr>
            <tr>
              <th scope="row"><?= Html::tag('span', Html::encode('σ'), [
                'title' => Yii::t('app', 'Standard Deviation'),
              ]) ?></th>
              <td><?= $fmt->asDecimal($model->sd_clear_waves, 4) ?></td>
              <td><?= $fmt->asDecimal($model->sd_golden_eggs, 3) ?></td>
              <td><?= $fmt->asDecimal($model->sd_power_eggs, 3) ?></td>
              <td><?= $fmt->asDecimal($model->sd_deaths, 3) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="wStatsRoot mb-2">
<?php $wStats = $model->getWeaponStats()
  ->with(['weapon'])
  ->orderBy([
    '([[cleared]]::double precision / [[plays]]::double precision)' => SORT_DESC,
    '[[plays]]' => SORT_DESC,
    '[[weapon_id]]' => SORT_ASC,
  ])
  ->all()
?>
<?php foreach ($wStats as $wStat) { ?>
        <div class="wStats">
          <p>
            <?= Html::img(
              $wAsset->getIconUrl($wStat->weapon->key),
              [
                'alt' => Yii::t('app-weapon2', $wStat->weapon->name),
                'title' => Yii::t('app-weapon2', $wStat->weapon->name),
                'class' => 'auto-tooltip',
              ]
            ) . "\n" ?>
          </p>
          <?= Html::tag('div', '', [
            'class' => ['chart', 'weapon-rate'],
            'data' => [
              'values' => [
                'cleared' => (int)$wStat->cleared,
                'failed' => (int)$wStat->plays - (int)$wStat->cleared,
              ],
              'labels' => $wLabels,
            ],
          ]) . "\n" ?>
        </div>
<?php } ?>
      </div>
    </div>
  </div>
<?php } ?>
  <hr>
  <p class="text-muted">
    *1) <?= Html::encode(Yii::t(
      'app-salmon2',
      'These statistics are based only on the results of games that have been cleared.'
    )) . "\n" ?>
  </p>
</div>
