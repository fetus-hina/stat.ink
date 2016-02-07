{{strip}}
  {{set layout="main.tpl"}}

  {{$weaponName = $weapon->name|translate:'app-weapon'}}
  {{$title = 'Weapon | {weapon}'|translate:'app':['weapon' => $weaponName]}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  <div class="container">
    <h1>
      {{$weaponName|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <p class="form-inline">
      <select id="change-weapon" class="form-control">
        {{use class="yii\helpers\Html"}}
        {{Html::renderSelectOptions($weapon->key, $weapons)}}
      </select>
      {{registerJs}}
        (function($) {
          var baseUrl = "{{url route="entire/weapon" weapon="WEAPON_KEY"}}";
          $('#change-weapon').change(function() {
            var $select = $(this);
            var url = baseUrl.replace(
              'WEAPON_KEY',
              function () {
                return encodeURIComponent($select.val());
              }
            );
            window.location.href = url;
          });
        })(jQuery);
      {{/registerJs}}
    </p>

    <p>
      {{foreach $rules as $rule}}
        {{if !$rule@first}} | {{/if}}
        <a href="#{{$rule->key|escape}}">
          {{$rule->name|escape}}
        </a>
      {{/foreach}}
    </p>
    
    <script>
      window.kddata = {{$killDeath|json_encode}};
    </script>

    {{foreach $rules as $rule}}
      <h2 id="{{$rule->key|escape}}">
        {{$rule->name|escape}}
      </h2>
      <h3>
        {{'Kills and Deaths'|translate:'app'|escape}}
      </h3>
      <p>
        {{'Kills (average):'|translate:'app'|escape}}&#32;<span class="kd-summary" data-rule="{{$rule->key|escape}}" data-type="kill-avg"></span><br>
        {{'Deaths (average):'|translate:'app'|escape}}&#32;<span class="kd-summary" data-rule="{{$rule->key|escape}}" data-type="death-avg"></span>
      </p>
      {{registerJs}}
        (function($){
          "use strict";
          var rules = {};
          $('.kd-summary').each(function() {
            var $this = $(this);
            var ruleKey = $this.attr('data-rule');
            var typeKey = $this.attr('data-type');
            if (!rules[ruleKey]) {
              var json = window.kddata[ruleKey];
              var maxKD = json.reduce(function(prev, cur) {
                return Math.max(prev, cur.kill, cur.death);
              }, 0);
              var kills = [];
              var deaths = [];
              var totalCount = 0;
              (function() {
                for(var i = 0; i <= maxKD; ++i) {
                  kills.push(0);
                  deaths.push(0);
                }
              })();
              $.each(json, function() {
                kills[this.kill] += this.battle;
                deaths[this.death] += this.battle;
                totalCount += this.battle;
              });
              var killAvg = (totalCount == 0)
                ? 'N/A'
                : (kills.reduce(function(p,c,i){return p+c*i}, 0) / totalCount).toFixed(2);
              var deathAvg = (totalCount == 0)
                ? 'N/A'
                : (deaths.reduce(function(p,c,i){return p+c*i}, 0) / totalCount).toFixed(2);
              /* Note: この実装は正しく中央値を計算していない（奇数の時で境界にあたるときずれる） */
              var findMedian = function (arr, total) {
                var current = 0;
                for (var i = 0; i < arr.length; ++i) {
                  current += arr[i];
                  if (current >= total / 2) {
                    return i;
                  }
                }
              };
              rules[ruleKey] = {
                'kill-avg': killAvg,
                'death-avg': deathAvg,
                'kill-mid': (totalCount == 0) ? 'N/A' : findMedian(kills, totalCount),
                'death-mid': (totalCount == 0) ? 'N/A' : findMedian(deaths, totalCount)
              };
            }
            $this.text(rules[ruleKey][typeKey]);
          });
        })(jQuery);
      {{/registerJs}}
      <div class="graph stat-kill-death" data-rule="{{$rule->key|escape}}">
      </div>
      {{\jp3cki\yii2\flot\FlotAsset::register($this)|@void}}
      {{registerCss}}
        .graph {
          height: 300px;
        }
      {{/registerCss}}
      {{registerJs}}
        (function($){
          "use strict";
          function update() {
            var $graphs = $('.graph.stat-kill-death');
            $graphs.height($graphs.width() * 9 / 16);
            $graphs.each(function(){
              var $graph = $(this);
              var ruleKey = $graph.attr('data-rule');
              var json = window.kddata[ruleKey];
              var maxKD = 30;
              var total = 0;
              var kills = [];
              var deaths = [];
              (function() {
                for(var i = 0; i <= maxKD; ++i) {
                  kills.push(0);
                  deaths.push(0);
                }
              })();
              $.each(json, function() {
                total += this.battle;
                if (maxKD >= this.kill) {
                  kills[this.kill] += this.battle;
                }
                if (maxKD >= this.death) {
                  deaths[this.death] += this.battle;
                }
              });
              var data = [
                {
                  label: "{{'Battles'|translate:'app'|escape:'javascript'}} ({{'Kill'|translate:'app'|escape:'javascript'}})",
                  data: kills.map(function(v, i) {
                    return [i - 0.5, v * 100 / total];
                  }),
                  color: window.colorScheme.win,
                },
                {
                  label: "{{'Battles'|translate:'app'|escape:'javascript'}} ({{'Death'|translate:'app'|escape:'javascript'}})",
                  data: deaths.map(function(v, i) {
                    return [i - 0.5, v * 100 / total];
                  }),
                  color: window.colorScheme.lose,
                }
              ];
              $.plot($graph, data, {
                xaxis: {
                  min: -0.5,
                  minTickSize: 1,
                  tickFormatter: function (v) {
                    return v + ' K, D';
                  }
                },
                yaxis: {
                  min: 0,
                  tickFormatter: function (v) {
                    return v.toFixed(1) + "%";
                  }
                },
                series: {
                  lines: {
                    show: true,
                    fill: true,
                    steps: true,
                  },
                },
              });
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
          });
        })(jQuery);
      {{/registerJs}}
      <h3>
        {{'Based on kills'|translate:'app'|escape}}
      </h3>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
          <div class="graph stat-wp" data-base="kill" data-rule="{{$rule->key|escape}}" data-scale="no">
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
          <div class="graph stat-wp" data-base="kill" data-rule="{{$rule->key|escape}}" data-scale="yes">
          </div>
        </div>
      </div>
      <h3>
        {{'Based on deaths'|translate:'app'|escape}}
      </h3>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
          <div class="graph stat-wp" data-base="death" data-rule="{{$rule->key|escape}}" data-scale="no">
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
          <div class="graph stat-wp" data-base="death" data-rule="{{$rule->key|escape}}" data-scale="yes">
          </div>
        </div>
      </div>
      {{\jp3cki\yii2\flot\FlotStackAsset::register($this)|@void}}
      {{registerJs}}
        (function($){
          "use strict";
          function update() {
            var $graphs = $('.graph.stat-wp');
            $graphs.height($graphs.width() * 9 / 16);
            $graphs.each(function(){
              var $graph = $(this);
              var ruleKey = $graph.attr('data-rule');
              var kdKey = $graph.attr('data-base');
              var scale = $graph.attr('data-scale') === 'yes';
              var json = window.kddata[ruleKey];
              var maxKD = 30;
              var win = [];
              var lose = [];
              (function() {
                for(var i = 0; i <= maxKD; ++i) {
                  win.push(0);
                  lose.push(0);
                }
              })();
              $.each(json, function() {
                if (maxKD >= this[kdKey]) {
                  win[this[kdKey]] += this.win;
                  lose[this[kdKey]] += this.battle - this.win;
                }
              });
              if (scale) {
                (function() {
                  for (var i = 0; i <= maxKD; ++i) {
                    var t = win[i] + lose[i];
                    if (t > 0) {
                      win[i] = win[i] * 100 / t;
                      lose[i] = lose[i] * 100 / t;
                    } else {
                      win[i] = lose[i] = 0;
                    }
                  }
                })();
              }
              var data = [
                {
                  label: scale
                    ? "{{'Win %'|translate:'app'|escape:'javascript'}} ({{'Win'|translate:'app'|escape:'javascript'}})"
                    : "{{'Battles'|translate:'app'|escape:'javascript'}} ({{'Win'|translate:'app'|escape:'javascript'}})",
                  data: win.map(function(v, i) {
                    return [i - 0.5, v];
                  }),
                  color: window.colorScheme.win,
                },
                {
                  label: scale
                    ? "{{'Win %'|translate:'app'|escape:'javascript'}} ({{'Lose'|translate:'app'|escape:'javascript'}})"
                    : "{{'Battles'|translate:'app'|escape:'javascript'}} ({{'Lose'|translate:'app'|escape:'javascript'}})",
                  data: lose.map(function(v, i) {
                    return [i - 0.5, v];
                  }),
                  color: window.colorScheme.lose,
                }
              ];
              $.plot($graph, data, {
                xaxis: {
                  min: -0.5,
                  minTickSize: 1,
                  tickFormatter: function (v) {
                    return v + (kdKey === 'kill' ? ' K' : ' D');
                  },
                },
                yaxis: {
                  min: 0,
                  max: scale ? 100 : undefined,
                  tickFormatter: function (v) {
                    return v.toFixed(1) + "%";
                  },
                  show: scale,
                },
                series: {
                  stack: !!scale,
                  lines: {
                    show: true,
                    fill: true,
                    steps: true,
                  },
                },
              });
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
          });
        })(jQuery);
      {{/registerJs}}
    {{/foreach}}
  </div>
  {{registerJs}}
    $(window).resize();
  {{/registerJs}}
{{/strip}}
