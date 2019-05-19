<?php
declare(strict_types=1);

use app\assets\UserStatGachiAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\helpers\Json;

$this->context->layout = 'main';
$title = Yii::t('app', '{0}\'s Battle Stats (Ranked Battle)', [
  $user->name,
]);
$this->title = $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

UserStatGachiAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
      <h2 id="exp"><?= Html::encode(Yii::t('app', 'Rank')) ?></h2>
      <div style="margin-bottom:15px">
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
            <div class="user-label"><?= Html::encode(Yii::t('app', 'Current')) ?></div>
            <div class="user-number"><?= $userRankStat
              ? Html::encode(sprintf('%s %s', $userRankStat->rank, $userRankStat->rankExp))
              : Html::encode(Yii::t('app', 'N/A'))
            ?></div>
          </div>
        </div>
      </div>
      <p><?= Html::encode(
        Yii::t('app', 'Excluded: Private Battles and Squad Battles (when Rank S or S+)')
      ) ?></p>

<?php $this->registerJs(vsprintf('$(%s).rankHistory($(%s), $(%s), %s, %s);', [
  Json::encode('.stat-rank'),
  Json::encode('#stat-rank-legend'),
  Json::encode('#show-rank-moving-avg'),
  Json::encode($recentRank),
  Json::encode([
    'area' => sprintf('%s (%s)', Yii::t('app', 'Rank'), Yii::t('app-rule', 'Splat Zones')),
    'yagura' => sprintf('%s (%s)', Yii::t('app', 'Rank'), Yii::t('app-rule', 'Tower Control')),
    'hoko' => sprintf('%s (%s)', Yii::t('app', 'Rank'), Yii::t('app-rule', 'Rainmaker')),
    'movingAvg10' => Yii::t('app', 'Moving Avg. ({0} Battles)', [10]),
    'movingAvg50' => Yii::t('app', 'Moving Avg. ({0} Battles)', [50]),
  ]),
])) ?>
      <div id="stat-rank-legend"></div>
      <div class="graph stat-rank"></div>
      <div class="graph stat-rank" data-limit="200"></div>
      <div class="text-right"><?php
        echo Html::tag('label', implode(' ', [
          Html::tag('input', '', [
            'type' => 'checkbox',
            'id' => 'show-rank-moving-avg',
            'value' => '1',
            'checked' => true,
          ]),
          Html::encode(Yii::t('app', 'Show moving averages')),
        ]));
      ?></div>
      <hr>
      <h2 id="wp"><?= Html::encode(Yii::t('app', 'Winning Percentage')) ?></h2>
      <p><?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) ?></p>
      <aside>
        <nav>
          <ul class="inline-list"><?= implode('', array_map(
            function (string $key, string $name): string {
              return Html::tag('li', Html::a(
                Html::encode($name),
                '#wp-' . $key
              ));
            },
            array_keys($maps),
            array_values($maps)
          )) ?></ul>
        </nav>
      </aside>
      <script>
        /* window._maps = {{$maps|array_keys|json_encode}}; */
        /* window._wpData = {{$recentWP|json_encode}}; */
      </script>
      <div id="stat-wp-legend"></div>
      <div class="graph stat-wp"></div>
      <div class="graph stat-wp" data-limit="200"></div>

<?php foreach ($maps as $mapKey => $mapName) { ?>
      <?= Html::tag(
        'h3',
        implode('', [
          Html::tag('span', Html::encode(Yii::t('app', 'Winning Percentage') . ' - '), [
            'clas' => 'hidden-xs',
          ]),
          Html::a(
            Html::encode($mapName),
            ['show/user',
              'screen_name' => $user->screen_name,
              'filter' => [
                'rule' => '@gachi',
                'map' => $mapKey,
              ],
            ]
          ),
        ]),
        ['id' => 'wp-' . $mapKey]
      ) . "\n" ?>
<?php foreach ([null, 200] as $limit) { ?>
      <?= Html::tag('div', '', [
        'class' => 'graph stat-map-wp',
        'data' => array_filter([
          'map' => $mapKey,
          'limit' => $limit,
        ]),
      ]) . "\n" ?>
<?php } ?>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
<?php __halt_compiler();
{{/strip}}
{{registerCss}}
  .stat-rank{height:300px}
{{/registerCss}}
{{registerJs}}
(function($) {
  var $graphs = $('.graph');
  var colorLock = window.colorLock;
  var colorScheme = {
    area:     colorLock ? window.colorScheme.area:    '#edc240',
    yagura:   colorLock ? window.colorScheme.yagura:  '#40a2ed',
    hoko:     colorLock ? window.colorScheme.hoko:    '#ed4040',
    moving1:  colorLock ? window.colorScheme.moving1: 'rgba(64,237,64,.5)',
    moving2:  colorLock ? window.colorScheme.moving2: 'rgba(148,64,237,.5)'
  };

  function drawWPGraph(json) {
    var $graph_ = $graphs.filter('.stat-wp');

    var rules = (function(json) {
      var ret = {
        area: [],
        yagura: [],
        hoko: []
      };
      var prevIndex = null;
      var prevRule = null;
      var prevValue = null;
      for (var i = 0; i < json.length; ++i) {
        var data = json[i];
        if (prevRule !== data.rule && prevRule !== null) {
          ret[prevRule].push([data.index, null]);
          ret[data.rule].push([prevIndex, prevValue]);
        }
        ret[data.rule].push([data.index, data.totalWP]);
        prevIndex = data.index;
        prevRule = data.rule;
        prevValue = data.totalWP;
      }
      return ret;
    })(json);

    var data = [
      {
        label: "{{'Winning Percentage'|translate:'app'|escape:'javascript'}} ({{'Splat Zones'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.area,
        color: colorScheme.area
      },
      {
        label: "{{'Winning Percentage'|translate:'app'|escape:'javascript'}} ({{'Tower Control'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.yagura,
        color: colorScheme.yagura
      },
      {
        label: "{{'Winning Percentage'|translate:'app'|escape:'javascript'}} ({{'Rainmaker'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.hoko,
        color: colorScheme.hoko
      },
      {
        label: "{{'Win % ({0} Battles)'|translate:'app':20|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingWP];
        }),
        color: colorScheme.moving1
      },
      {
        label: "{{'Win % ({0} Battles)'|translate:'app':50|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingWP50];
        }),
        color: colorScheme.moving2
      }
    ];

    $graph_.each(function() {
      var $graph = $(this);
      var limit = ~~$graph.attr('data-limit');
      if (limit > 0 && json.length <= limit) {
        $graph.hide();
        return;
      }

      $.plot($graph, data, {
        xaxis: {
          min: limit > 0 ? -limit : null,
          minTickSize: 1,
          tickFormatter: function (v) {
            return ~~v;
          }
        },
        yaxis: {
          min: 0,
          max: 100,
        },
        legend: {
          container: $('#stat-wp-legend')
        }
      });
    });
  }

  function drawMapWPGraph(mapKey, json) {
    var $graph_ = $graphs.filter('.stat-map-wp').filter(function() {
      return $(this).attr('data-map') == mapKey;
    });

    {{* そのマップだけのデータに絞込 *}}
    json = $.extend(true, [], json.filter(function(row) {
      return row.map == mapKey;
    }));

    {{* データの付け替え *}}
    var count = json.length;
    var winCount = 0;
    var results = [];
    $.each(json, function(index) {
      var row = this;
      row.index = (index + 1) - count;
      if (row.is_win) {
        ++winCount;
      }
      row.totalWP = winCount * 100 / (index + 1);

      row.movingWP = null;
      row.movingWP50 = null;
      if (results.unshift(row.is_win) > 50) {
        results.pop();
      }
      if (results.length >= 20) {
        row.movingWP = results.slice(0, 20).filter(function(a){return a}).length * 100 / 20;
        if (results.length >= 50) {
          row.movingWP50 = results.slice(0, 50).filter(function(a){return a}).length * 100 / 50;
        }
      }
    });

    var rules = (function(json) {
      var ret = {
        area: [],
        yagura: [],
        hoko: []
      };
      var prevIndex = null;
      var prevRule = null;
      var prevValue = null;
      for (var i = 0; i < json.length; ++i) {
        var data = json[i];
        if (prevRule !== data.rule && prevRule !== null) {
          ret[prevRule].push([data.index, null]);
          ret[data.rule].push([prevIndex, prevValue]);
        }
        ret[data.rule].push([data.index, data.totalWP]);
        prevIndex = data.index;
        prevRule = data.rule;
        prevValue = data.totalWP;
      }
      return ret;
    })(json);

    var data = [
      {
        label: "{{'Winning Percentage'|translate:'app'|escape:'javascript'}} ({{'Splat Zones'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.area,
        color: colorScheme.area
      },
      {
        label: "{{'Winning Percentage'|translate:'app'|escape:'javascript'}} ({{'Tower Control'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.yagura,
        color: colorScheme.yagura
      },
      {
        label: "{{'Winning Percentage'|translate:'app'|escape:'javascript'}} ({{'Rainmaker'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.hoko,
        color: colorScheme.hoko
      },
      {
        label: "{{'Win % ({0} Battles)'|translate:'app':20|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingWP];
        }),
        color: colorScheme.moving1
      },
      {
        label: "{{'Win % ({0} Battles)'|translate:'app':50|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingWP50];
        }),
        color: colorScheme.moving2
      }
    ];

    $graph_.each(function() {
      var $graph = $(this);
      var limit = ~~$graph.attr('data-limit');
      if (limit > 0 && json.length <= limit) {
        $graph.hide();
        return;
      }

      $.plot($graph, data, {
        xaxis: {
          min: limit > 0 ? -limit : null,
          minTickSize: 1,
          tickFormatter: function (v) {
            return ~~v;
          }
        },
        yaxis: {
          min: 0,
          max: 100,
        },
        legend: {
          container: $('#stat-wp-legend')
        }
      });
    });
  }

  var timerId = null;
  $(window).resize(function() {
    if (timerId !== null) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(function() {
      $graphs.height($graphs.width() * 9 / 16);
      drawWPGraph(window._wpData);
      $.each(window._maps, function () {
        drawMapWPGraph(this, window._wpData);
      });
    }, 33);
  }).resize();

  $('#show-rank-moving-avg').click(function () {
    $(window).resize();
  });
})(jQuery);
{{/registerJs}}
