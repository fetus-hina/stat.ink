{{strip}}
  {{set layout="main.tpl"}}

  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "{0}'s Battle Stats (Ranked Battle)"|translate:'app':$name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  {{\jp3cki\yii2\flot\FlotAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{SnsWidget}}

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <h2 id="exp">
          {{'Rank'|translate:'app'|escape}}
        </h2>
        <div style="margin-bottom:15px">
          <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
              <div class="user-label">{{'Current'|translate:'app'|escape}}</div>
              <div class="user-number">
                {{if $userRankStat}}
                  {{$userRankStat->rank|escape}} {{$userRankStat->rankExp}}
                {{else}}
                  ?
                {{/if}}
              </div>
            </div>
          </div>
        </div>
        <p>
          {{'Excluded: Private Battles and Squad Battles(when Rank S, S+)'|translate:'app'|escape}}
        </p>
        <script>
          window._rankData = {{$recentRank|json_encode}};
        </script>
        <div id="stat-rank-legend"></div>
        <div class="graph stat-rank"></div>
        <div class="graph stat-rank" data-limit="200"></div>
        <div class="text-right">
          <label>
            <input type="checkbox" id="show-rank-moving-avg" value="1" checked> {{'Show moving averages'|translate:'app'|escape}}
          </label>
        </div>

        <hr>

        <h2 id="wp">
          {{'Winning Percentage'|translate:'app'|escape}}
        </h2>
        <p>
          {{'Excluded: Private Battles'|translate:'app'|escape}}
        </p>
        <p>
          {{foreach $maps as $mapKey => $mapName}}
            {{if !$mapName@first}}
              &#32;|&#32;
            {{/if}}
            <a href="#wp-{{$mapKey|escape}}">
              {{$mapName|escape}}
            </a>
          {{/foreach}}
        </p>
        <script>
          window._maps = {{$maps|array_keys|json_encode}};
          window._wpData = {{$recentWP|json_encode}};
        </script>
        <div id="stat-wp-legend"></div>
        <div class="graph stat-wp"></div>
        <div class="graph stat-wp" data-limit="200"></div>

        {{foreach $maps as $mapKey => $mapName}}
          <h3 id="wp-{{$mapKey|escape}}">
            {{$filter = [
                'rule' => '@gachi',
                'map' => $mapKey
              ]}}
            <span class="hidden-xs">{{'Winning Percentage'|translate:'app'|escape}} - </span>
            <a href="{{url route="show/user" screen_name=$user->screen_name filter=$filter}}">
              {{$mapName|escape}}
            </a>
          </h3>
          <div class="graph stat-map-wp" data-map="{{$mapKey|escape}}"></div>
          <div class="graph stat-map-wp" data-map="{{$mapKey|escape}}" data-limit="200"></div>
        {{/foreach}}
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
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
  
  function drawRankGraph(json) {
    var $graph_ = $graphs.filter('.stat-rank');

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
        ret[data.rule].push([data.index, data.exp]);
        prevIndex = data.index;
        prevRule = data.rule;
        prevValue = data.exp;
      }
      return ret;
    })(json);

    var data = [
      {
        label: "{{'Rank'|translate:'app'|escape:'javascript'}} ({{'Splat Zones'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.area,
        color: colorScheme.area
      },
      {
        label: "{{'Rank'|translate:'app'|escape:'javascript'}} ({{'Tower Control'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.yagura,
        color: colorScheme.yagura
      },
      {
        label: "{{'Rank'|translate:'app'|escape:'javascript'}} ({{'Rainmaker'|translate:'app-rule'|escape:'javascript'}})",
        data: rules.hoko,
        color: colorScheme.hoko
      }
    ];

    if ($('#show-rank-moving-avg').prop('checked')) {
      data.push({
        label: "{{'Moving Avg. ({0} Battles)'|translate:'app':10|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingAvg];
        }),
        color: colorScheme.moving1
      });
      data.push({
        label: "{{'Moving Avg. ({0} Battles)'|translate:'app':50|escape}}",
        data: json.map(function(v) {
          return [v.index, v.movingAvg50];
        }),
        color: colorScheme.moving2
      });
    }

    $graph_.each(function() {
      var $graph = $(this);
      var limit = ~~$graph.attr('data-limit');
      if (limit > 0 && json.length <= limit) {
        $graph.hide();
        return;
      }

      $.plot($graph, data, {
        xaxis: {
          minTickSize: 1,
          min: limit > 0 ? -limit : null,
          tickFormatter: function (v) {
            return ~~v;
          }
        },
        yaxis: {
          minTickSize: 10,
          tickFormatter: function (v) {
            if (v >= 1100) {
              return v > 1100 ? '' : 'S+ 99';
            } else if (v < 0) {
              return '';
            }

            var rank = Math.floor(v / 100);
            var exp = v % 100;
            var ranks = ['C-', 'C', 'C+', 'B-', 'B', 'B+', 'A-', 'A', 'A+', 'S', 'S+'];
            return ranks[rank] + " " + exp;
          }
        },
        legend: {
          container: $('#stat-rank-legend')
        }
      });
    });
  }

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
      drawRankGraph(window._rankData);
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
