<?php
declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\FA;
use app\components\widgets\SnsWidget;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\Progress;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;

$this->context->layout = 'main';

$title = Yii::t('app', 'Weapons');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <nav><?= Nav::widget([
    'options' => [
      'class' =>'nav-tabs',
    ],
    'items' => [
      [
        'label' => 'Splatoon 2',
        'url' => ['entire/weapons2'],
      ],
      [
        'label' => 'Splatoon',
        'url' => 'javascript:void(0);',
        'active' => true,
      ],
    ],
  ]) ?></nav>

  <h2><?= Html::encode(Yii::t('app', 'Weapons')) ?></h2>
  <p><?= Html::encode(
    Yii::t(
      'app',
      'Excluded: The uploader, All players (Private Battle), Uploader\'s teammates (Squad Battle or Splatfest Battle)'
    )
  ) ?></p>
  <p><?= Html::encode(
    Yii::t(
      'app',
      '* This exclusion is in attempt to minimize overcounting in weapon usage statistics.'
    )
  ) ?></p>
  
  <nav><ul class="inline-list"><?= implode('', array_map(
    function (stdClass $rule): string {
      return Html::tag(
        'li',
        Html::a(
          Html::encode($rule->name),
          ['entire/weapons', '#' => sprintf('weapon-%s', $rule->key)]
        )
      );
    },
    $entire
  )) ?></ul></nav>

<?php FlotAsset::register($this) ?>
<?php FlotStackAsset::register($this) ?>
<?php FlotTimeAsset::register($this) ?>
  <h3 id="trends"><?= Html::encode(Yii::t('app', 'Trends')) ?></h3>
  <p><?= Html::a(
    implode(' ', [
      (string)FA::fas('exchange-alt')->fw(),
      Html::encode(Yii::t('app', 'Compare number of uses')),
    ]),
    ['entire/weapons-use'],
    ['class' => 'btn btn-default']
  ) ?></p>
  <div id="graph-trends-legends"></div>
  <div id="graph-trends" class="graph">
  </div>
  <p class="text-right">
    <label><?= implode(' ', [
      Html::tag('input', '', [
        'type' => 'checkbox',
        'id' => 'stack-trends',
        'value' => 1,
        'checked' => true,
      ]),
      Html::encode(Yii::t('app', 'Stack')),
    ]) ?></label>
  </p>
  <?= Html::tag('script', Json::encode($uses), [
    'id' => 'trends-json',
    'type' => 'application/json',
  ]) . "\n" ?>
  {{registerJs}}
    (function($){
      "use strict";
      var stack = true;
      function update() {
        var formatDate=function(date){
          function zero(n){
            n=n+"";
            return(n.length== 1)?"0"+n:n;
          }
          return date.getUTCFullYear()+"-"+zero(date.getUTCMonth()+1)+"-"+zero(date.getUTCDate());
        };
        var date2unixTime=function(d){
          return(new Date(d+'T00:00:00Z')).getTime();
        };
        var $graphs = $('#graph-trends');
        var json = JSON.parse($('#trends-json').text());
        var data = [];
        for (var i = 0; i < json[0].weapons.length; ++i) {
          var weapon = json[0].weapons[i];
          data.push({
            label: json[0].weapons[i].name,
            data: json.map(function(week) {
              return [
                date2unixTime(week.date),
                week.weapons[i].pct
              ];
            }),
          });
        }
        if (stack) {
          data.push({
            label: "{{'Others'|translate:'app'|escape:javascript}}",
            data: json.map(function(week){
              return [
                date2unixTime(week.date),
                week.others_pct
              ];
            }),
            color: '#cccccc'
          });
        }
        $graphs.height($graphs.width() * 9 / 16);
        $graphs.each(function(){
          var $graph = $(this);
          $.plot($graph, data, {
            xaxis:{
              mode:'time',
              minTickSize:[7,'day'],
              tickFormatter:function(v){
                return formatDate(new Date(v));
              }
            },
            yaxis: {
              min: 0,
              max: stack ? 100 : undefined,
              tickFormatter:function(v){
                return v.toFixed(1)+"%";
              }
            },
            series: {
              stack: stack,
              points: {
                show: !stack,
              },
              lines: {
                show: true,
                fill: stack,
                steps: false,
              }
            },
            legend: {
              sorted: stack ? "reverse" : false,
              position: "nw",
              container: $('#graph-trends-legends'),
              noColumns: (function() {
                var width = $(window).width();
                if (width < 768) {
                  return 1;
                } else if (width < 992) {
                  return 2;
                } else if (width < 1200) {
                  return 4;
                } else {
                  return 5;
                }
              })()
            }
          });
          window.setTimeout(function () {
            var $labels = $('td.legendLabel', $('#graph-trends-legends'));
            $labels.width(
              Math.max.apply(null, $labels.map(function () {
                return $(this).width('').width();
              })) + 12
            );
          }, 1);
        });
      }
      var timerId = null;
      $(window).resize(function() {
        if (timerId !== null) {
          window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(function() {
          update();
        }, 33);
      }).resize();

      $('#stack-trends').click(function () {
        stack = !!$(this).prop('checked');
        $(window).resize();
      });
    })(jQuery);
  {{/registerJs}}

<?php foreach ($entire as $rule) { ?>
<?php if ($rule->data->battle_count > 0) { ?>
  <?= Html::tag('h3', Html::encode($rule->name), [
    'id' => sprintf('weapon-%s', $rule->key),
  ]) . "\n" ?>
  <p><?= Html::encode(implode(', ', [
    vsprintf('%s %s', [
      Yii::t('app', 'Battles:'),
      Yii::$app->formatter->asDecimal((int)$rule->data->battle_count),
    ]),
    vsprintf('%s %s', [
      Yii::t('app', 'Players:'),
      Yii::$app->formatter->asDecimal((int)$rule->data->player_count),
    ]),
  ])) ?></p>
<?php SortableTableAsset::register($this) ?>
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => $rule->data->weapons,
      'sort' => false,
      'pagination' => false,
    ]),
    'layout' => '{items}',
    'options' => [
      'class' => 'table-responsive',
    ],
    'tableOptions' => [
      'class' => 'table table-striped table-condensed table-sortable',
    ],
    'columns' => array_filter([
      [
        'label' => Yii::t('app', 'Weapon'), // {{{
        'headerOptions' => ['data-sort' => 'string'],
        'contentOptions' => function (stdClass $w): array {
          return ['data-sort-value' => $w->name];
        },
        'format' => 'raw',
        'value' => function (stdClass $w) use ($rule): string {
          return Html::a(
            Html::encode($w->name),
            ['entire/weapon', 'weapon' => $w->key, 'rule' => $rule->key],
            [
              'class' => 'auto-tooltip',
              'title' => implode(' / ', [
                implode('', [
                  Yii::t('app', 'Sub:'),
                  $w->subweapon->name ?? '',
                ]),
                implode('', [
                  Yii::t('app', 'Special:'),
                  $w->special->name ?? '',
                ]),
              ]),
            ]
          );
        },
        // }}}
      ],
      [
        // Players {{{
        'label' => implode(' ', [
          Html::encode(Yii::t('app', 'Players')),
          Html::tag('span', (string)FA::fas('angle-down'), ['class' => 'arrow']),
        ]),
        'encodeLabel' => false,
        'headerOptions' => ['data-sort' => 'int'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(int)$w->count,
          ];
        },
        'format' => 'raw',
        'value' => function (stdClass $w) use ($rule): string {
          if ($w->count < 1) {
            return Yii::$app->formatter->asInteger(0);
          }
          
          return Html::tag('span', Yii::$app->formatter->asInteger($w->count), [
            'class' => 'auto-tooltip',
            'title' => Yii::$app->formatter->asPercent($w->count / $rule->data->player_count, 2),
          ]);
        },
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg Kills'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(float)$w->avg_kill,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'avg_kill',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg Deaths'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(float)$w->avg_death,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'avg_death',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg KR'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->kr,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'kr',
        // }}}
      ],
      $rule->key === 'nawabari'
        ? [
          'label' => Yii::t('app', 'Avg Inked'), // {{{
          'headerOptions' => ['data-sort' => 'float'],
          'contentOptions' => function (stdClass $w): array {
            return [
              'class' => 'text-right',
              'data-sort-value' => $w->avg_inked,
            ];
          },
          'format' => ['decimal', 1],
          'attribute' => 'avg_inked',
          // }}}
        ]
        : null,
      [
        'label' => Yii::t('app', 'Win %'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->wp,
          ];
        },
        'format' => ['percent', 2],
        'attribute' => 'wp',
        // }}}
      ],
    ]),
  ]) . "\n" ?>
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => $rule->sub,
      'sort' => false,
      'pagination' => false,
    ]),
    'layout' => '{items}',
    'options' => [
      'class' => 'table-responsive',
    ],
    'tableOptions' => [
      'class' => 'table table-striped table-condensed table-sortable',
      'id' => sprintf('sub-%s', $rule->key),
    ],
    'columns' => array_filter([
      [
        'label' => Yii::t('app', 'Sub Weapon'), // {{{
        'headerOptions' => ['data-sort' => 'string'],
        'attribute' => 'name',
        // }}}
      ],
      [
        // Players {{{
        'label' => implode(' ', [
          Html::encode(Yii::t('app', 'Players')),
          Html::tag('span', (string)FA::fas('angle-down'), ['class' => 'arrow']),
        ]),
        'encodeLabel' => false,
        'headerOptions' => ['data-sort' => 'int'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(int)$w->count,
          ];
        },
        'format' => 'raw',
        'value' => function (stdClass $w) use ($rule): string {
          if ($w->count < 1) {
            return Yii::$app->formatter->asInteger(0);
          }
          
          return Html::tag('span', Yii::$app->formatter->asInteger($w->count), [
            'class' => 'auto-tooltip',
            'title' => Yii::$app->formatter->asPercent($w->count / $rule->data->player_count, 2),
          ]);
        },
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg Kills'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(float)$w->avg_kill,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'avg_kill',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg Deaths'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(float)$w->avg_death,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'avg_death',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg KR'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->kr,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'kr',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Win %'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->wp,
          ];
        },
        'format' => ['percent', 2],
        'attribute' => 'wp',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Encounter Ratio'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->encounter_4,
          ];
        },
        'format' => ['percent', 2],
        'attribute' => 'encounter_4',
        // }}}
      ],
    ]),
  ]) . "\n" ?>
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => $rule->special,
      'sort' => false,
      'pagination' => false,
    ]),
    'layout' => '{items}',
    'options' => [
      'class' => 'table-responsive',
    ],
    'tableOptions' => [
      'class' => 'table table-striped table-condensed table-sortable',
      'id' => sprintf('special-%s', $rule->key),
    ],
    'columns' => array_filter([
      [
        'label' => Yii::t('app', 'Special'), // {{{
        'headerOptions' => ['data-sort' => 'string'],
        'attribute' => 'name',
        // }}}
      ],
      [
        // Players {{{
        'label' => implode(' ', [
          Html::encode(Yii::t('app', 'Players')),
          Html::tag('span', (string)FA::fas('angle-down'), ['class' => 'arrow']),
        ]),
        'encodeLabel' => false,
        'headerOptions' => ['data-sort' => 'int'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(int)$w->count,
          ];
        },
        'format' => 'raw',
        'value' => function (stdClass $w) use ($rule): string {
          if ($w->count < 1) {
            return Yii::$app->formatter->asInteger(0);
          }
          
          return Html::tag('span', Yii::$app->formatter->asInteger($w->count), [
            'class' => 'auto-tooltip',
            'title' => Yii::$app->formatter->asPercent($w->count / $rule->data->player_count, 2),
          ]);
        },
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg Kills'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(float)$w->avg_kill,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'avg_kill',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg Deaths'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => (string)(float)$w->avg_death,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'avg_death',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Avg KR'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->kr,
          ];
        },
        'format' => ['decimal', 2],
        'attribute' => 'kr',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Win %'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->wp,
          ];
        },
        'format' => ['percent', 2],
        'attribute' => 'wp',
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Encounter Ratio'), // {{{
        'headerOptions' => ['data-sort' => 'float'],
        'contentOptions' => function (stdClass $w): array {
          return [
            'class' => 'text-right',
            'data-sort-value' => $w->encounter_4,
          ];
        },
        'format' => ['percent', 2],
        'attribute' => 'encounter_4',
        // }}}
      ],
    ]),
  ]) . "\n" ?>
<?php } ?>
<?php } ?>

  <h2><?= Html::encode(
    Yii::t('app', 'Favorite Weapons of This Site Member')
  ) ?></h2>
<?php $_max = max(array_map(
  function (stdClass $a): int {
    return $a->user_count;
  },
  $users
)) ?>
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => $users,
      'sort' => false,
      'pagination' => false,
    ]),
    'layout' => '{items}',
    'options' => [
      'class' => 'table-responsive',
    ],
    'tableOptions' => [
      'class' => 'table table-striped table-condensed',
    ],
    'columns' => [
      [
        'label' => Yii::t('app', 'Weapon'),
        'value' => function (stdClass $row): string {
          return Yii::t('app-weapon', $row->weapon->name ?? '?');
        }
      ],
      [
        'label' => Yii::t('app', 'Users'),
        'format' => 'raw',
        'value' => function (stdClass $row) use ($_max): string {
          return Progress::widget([
            'percent' => 100 * $row->user_count / $_max,
            'label' => Yii::$app->formatter->asInteger($row->user_count),
          ]);
        },
      ],
    ],
  ]) . "\n" ?>
</div>
