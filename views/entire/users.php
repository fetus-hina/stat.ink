<?php

declare(strict_types=1);

use Base32\Base32;
use app\components\helpers\Html;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var array $agents
 * @var array $agents2
 * @var array[] $posts
 * @var array[] $posts2
 * @var string[] $agentNames
 * @var string[] $combineds
 */

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
      <?= $this->render('_users_2', ['agents' => $agents2]) . "\n" ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="spl1">
      <?= $this->render('_users_1', compact('combineds', 'agentNames', 'agents')) . "\n" ?>
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
