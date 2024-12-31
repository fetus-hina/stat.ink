<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 */

$title = Yii::t('app', 'Battles and Users');
$this->title = sprintf('%s | %s', $title, Yii::$app->name);

OgpHelper::default($this, title: $this->title);

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
  <?= Html::tag(
    'script',
    Json::encode($posts3, 0),
    ['id' => 'posts3', 'type' => 'application/json']
  ) . "\n" ?>
  <?= Tabs::widget([
    'items' => [
      [
        'encode' => false,
        'label' => implode(' ', [
          Icon::splatoon3(),
          Html::encode(Yii::t('app', 'Splatoon 3')),
        ]),
        'content' => $this->render('users/splatoon3', ['agents' => $agents3]),
        'active' => true,
      ],
      [
        'encode' => false,
        'label' => implode(' ', [
          Icon::splatoon2(),
          Html::encode(Yii::t('app', 'Splatoon 2')),
        ]),
        'content' => $this->render('users/splatoon2', ['agents' => $agents2]),
        'active' => false,
      ],
      [
        'encode' => false,
        'label' => implode(' ', [
          Icon::splatoon1(),
          Html::encode(Yii::t('app', 'Splatoon')),
        ]),
        'content' => $this->render('users/splatoon1', compact('combineds', 'agentNames', 'agents')),
        'active' => false,
      ],
    ],
    'options' => [
      'class' => ['mb-3'],
    ],
  ]) . "\n" ?>
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
