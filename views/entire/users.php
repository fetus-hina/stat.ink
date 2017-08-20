<?php
use Base32\Base32;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\grid\GridView;

$title = Yii::t('app', 'Battles and Users');
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$this->registerCss('#graph{height:300px;margin-bottom:10px}');

FlotAsset::register($this);
FlotTimeAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n"?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
  <?= Html::tag(
    'script',
    Json::encode($posts, 0),
    ['id' => 'posts1', 'type' => 'application/json']
  ) . "\n" ?>
  <?= Html::tag(
    'script',
    Json::encode($posts2, 0),
    ['id' => 'posts2', 'type' => 'application/json']
  ) . "\n" ?>
  <ul class="nav nav-tabs" role="tablist" style="margin-bottom:15px">
    <li role="presentation" class="active"><a href="#spl2" aria-controls="Splatoon 2" role="tab" data-toggle="tab">Splatoon 2</a></li>
    <li role="presentation"><a href="#spl1" aria-controls="Splatoon" role="tab" data-toggle="tab">Splatoon</a></li>
  </ul>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="spl2">
      <?= Html::tag(
        'div',
        '',
        [
          'class' => 'graph',
          'data' => [
            'ref' => 'posts2',
            'label-battle' => Yii::t('app', 'Battles'),
            'label-user' => Yii::t('app', 'Users'),
          ],
        ]
      ) . "\n" ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="spl1">
      <?= Html::tag(
        'div',
        '',
        [
          'class' => 'graph',
          'data' => [
            'ref' => 'posts1',
            'label-battle' => Yii::t('app', 'Battles'),
            'label-user' => Yii::t('app', 'Users'),
          ],
        ]
      ) . "\n" ?>
<?php if ($combineds): ?>
      <p>
        <?= implode(' | ', array_map(
          function (string $name) : string {
            return Html::a(
              Html::encode(mb_strimwidth($name, 0, 20, '…', 'UTF-8')),
              ['entire/combined-agent',
                'b32name' => rtrim(strtolower(Base32::encode($name)), '='),
              ]
            );
          },
          $combineds
        )) . "\n" ?>
      </p>
<?php endif; ?>
<?php if ($agentNames): ?>
      <p>
        <?= implode(' | ', array_map(
          function (string $name) : string {
            return Html::a(
              Html::encode(mb_strimwidth($name, 0, 20, '…', 'UTF-8')),
              ['entire/agent',
                'b32name' => rtrim(strtolower(Base32::encode($name)), '='),
              ]
            );
          },
          $agentNames)) . "\n" ?>
      </p>
<?php endif; ?>
      <h2>
        <?= Html::encode(Yii::t('app', 'User Agents in last 24 hours')) . "\n" ?>
      </h2>
      <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
          'allModels' => $agents,
        ]),
        'tableOptions' => [
          'class' => 'table table-striped',
        ],
        'columns' => [
          [
            'attribute' => 'battle',
            'label' => Yii::t('app', 'Battles'),
            'format' => 'integer',
            'contentOptions' => [
              'class' => 'text-right',
            ],
          ],
          [
            'attribute' => 'user',
            'label' => Yii::t('app', 'Users'),
            'format' => 'integer',
            'contentOptions' => [
              'class' => 'text-right',
            ],
          ],
          [
            'label' => Yii::t('app', 'User Agent'),
            'format' => 'raw',
            'value' => function (array $model) : string {
              return sprintf(
                '%s / %s',
                $model['agent_prod_url']
                  ? Html::a(Html::encode($model['agent_name']), $model['agent_prod_url'])
                  : Html::encode($model['agent_name']),
                $model['agent_rev_url']
                  ? Html::a(Html::encode($model['agent_version']), $model['agent_rev_url'])
                  : Html::encode($model['agent_version'])
              );
            },
          ],
        ],
      ]) . "\n" ?>
    </div>
  </div>
</div>
<?php
$this->registerJs(<<<'EoJS'
(function($) {
  var $graphs = $('.graph');

  function drawGraph($graph) {
    function dateToUnixTime(d) {
      return (new Date(d + 'T00:00:00Z')).getTime();
    }
    function formatDate(date) {
      function zero(n) {
        n = String(n);
        return (n.length == 1) ? "0" + n : n;
      }
      return date.getUTCFullYear() + "-" + zero(date.getUTCMonth() + 1) + "-" + zero(date.getUTCDate());
    }

    var json = JSON.parse($('#' + $graph.attr('data-ref')).text());
    var data = [
      {
        label: '<span class="fa fa-fw fa-arrow-left"></span>' + $graph.attr('data-label-battle'),
        data:json.map(function(v){return[dateToUnixTime(v.date),v.battle]}),
        bars:{
          show:true,
          align: "center",
          barWidth: 86400*1000*.8,
          lineWidth:1
        },
        color:window.colorScheme.graph1
      },
      {
        label: '<span class="fa fa-fw fa-arrow-right"></span>' + $graph.attr('data-label-user'),
        data:json.map(function(v){return[dateToUnixTime(v.date),v.user]}),
        yaxis:2,
        color:window.colorScheme.graph2
      }
    ];
    $.plot($graph, data, {
      xaxis: {
        mode:'time',
        minTickSize:[1,'day'],
        tickFormatter: function(v) {
          return formatDate(new Date(v));
        }
      },
      yaxis: {
        min:0,
        minTickSize:1,
        tickFormatter: function(v) {
          return ~~v;
        },
      },
      y2axis: {
        min:0,
        minTickSize:1,
        tickFormatter: function(v) {
          return ~~v;
        },
        position:'right'
      },
      legend: {
        position: "nw"
      }
    });
  }

  var timerId = null;
  $(window).resize(function() {
    if (timerId !== null) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(function() {
      $graphs.each(function(){
        var $graph = $(this);
        if ($graph.width() > 0) {
          $graph.height(Math.ceil($graph.width() * 9 / 16));
          drawGraph($graph);
        }
      });
    }, 33);
  }).resize();

  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    $(window).resize();
  });
})(jQuery);
EoJS
);
