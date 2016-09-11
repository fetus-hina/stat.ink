{{strip}}
  {{set layout="main.tpl"}}

  {{$weaponName = $weapon->name|translate:'app-weapon'}}
  {{$ruleName = $rule->name|translate:'app-rule'}}
  {{$title = 'Weapon | {weapon} | {rule}'|translate:'app':['weapon' => $weaponName, 'rule' => $ruleName]}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  {{\jp3cki\yii2\flot\FlotAsset::register($this)|@void}}
  {{\jp3cki\yii2\flot\FlotTimeAsset::register($this)|@void}}
  {{registerCss}}.graph{height:300px}{{/registerCss}}

  <div class="container">
    <h1>
      {{$weaponName|escape}} - {{$ruleName}}
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
          var baseUrl = "{{url route="entire/weapon" weapon="WEAPON_KEY" rule=$rule->key}}";
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
      {{foreach $rules as $_rule}}
        {{if !$_rule@first}} | {{/if}}
        {{if $_rule->key !== $rule->key}}
          <a href="{{url route="entire/weapon" weapon=$weapon->key rule=$_rule->key}}">
            {{$_rule->name|escape}}
          </a>
        {{else}}
          {{$_rule->name|escape}}
        {{/if}}
      {{/foreach}}
    </p>
    
    <script>
      window.kddata = {{$killDeath|json_encode}};
      window.mapdata = {{$mapWP|json_encode}};
    </script>
    <script id="use-pct-json" type="application/json">
      {{$useCount|json_encode}}
    </script>

    <h2 id="{{$rule->key|escape}}">
      {{$rule->name|translate:'app-rule'|escape}}
    </h2>
    <h3>
      {{'Use %'|translate:'app'|escape}}
    </h3>
    <p>
      {{$_form = [
          'weapon1' => $weapon->key,
          'rule1' => 'nawabari',
          'weapon2' => $weapon->key,
          'rule2' => 'area',
          'weapon3' => $weapon->key,
          'rule3' => 'yagura',
          'weapon4' => $weapon->key,
          'rule4' => 'hoko',
          'weapon5' => $weapon->key,
          'rule5' => '@gachi'
        ]}}
      <a href="{{url route="entire/weapons-use" cmp=$_form}}" class="btn btn-default">
        <span class="fa fa-exchange fa-fw"></span>&#32;{{'Compare number of uses'|translate:'app'|escape}}
      </a>
    </p>
    <div class="graph stat-use-pct">
    </div>
    {{registerJs}}
      (function($){
        "use strict";
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
          var $graphs = $('.graph.stat-use-pct');
          var $tooltip = $('<span>')
                .css({
                  position: "absolute",
                  display: "none",
                  padding: "2px",
                  backgroundColor: "#fff",
                  opacity: 0.9,
                  fontSize: "12px",
                })
                .appendTo('body');
          var json = JSON.parse($('#use-pct-json').text());
          var data = [
            {
              label:"{{'Use %'|translate:'app'|escape:javascript}}",
              data:json.map(function(v){
                return[
                  date2unixTime(v.date),
                  v.use_pct
                ];
              }),
              color:window.colorScheme.graph1
            }
          ];
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
                tickFormatter:function(v){
                  return v.toFixed(2)+"%";
                }
              },
              series: {
                points: {
                  show: true,
                },
                lines: {
                  show: true,
                  fill: true,
                  steps: false
                }
              },
              grid: {
                hoverable: true
              }
            });
          });
          $graphs.on('plothover',function(event,pos,item){
            if(item){
              var date=item.datapoint[0];
              var pct=item.datapoint[1].toFixed(3) + "%";
              $tooltip
                .text(
                  formatDate(new Date(date)) + '/' +
                  formatDate(new Date(date + 6 * 86400000)) + ' : ' +
                  pct
                )
                .css({
                  top: item.pageY - 20,
                  left: item.pageX <= $(window).width() / 2
                    ? item.pageX + 10
                    : item.pageX - ($tooltip.width() + 10)
                })
                .show();
            }else{
              $tooltip.hide();
            }
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
      {{'Kills and Deaths'|translate:'app'|escape}}
    </h3>
    <p>
      {{'Kills (average):'|translate:'app'|escape}}&#32;<span class="kd-summary" data-type="kill-avg"></span><br>
      {{'Deaths (average):'|translate:'app'|escape}}&#32;<span class="kd-summary" data-type="death-avg"></span>
    </p>
    {{registerJs}}
      (function($){
        "use strict";
        var summary = undefined;
        $('.kd-summary').each(function() {
          var $this = $(this);
          var typeKey = $this.attr('data-type');
          if (!summary) {
            var json = window.kddata;
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
            summary = {
              'kill-avg': killAvg,
              'death-avg': deathAvg,
            };
          }
          $this.text(summary[typeKey]);
        });
      })(jQuery);
    {{/registerJs}}
    <div class="graph stat-kill-death">
    </div>
    {{registerJs}}
      (function($){
        "use strict";
        function update() {
          var $graphs = $('.graph.stat-kill-death');
          $graphs.height($graphs.width() * 9 / 16);
          $graphs.each(function(){
            var $graph = $(this);
            var json = window.kddata;
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
        <div class="graph stat-wp" data-base="kill" data-scale="no">
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
        <div class="graph stat-wp" data-base="kill" data-scale="yes">
        </div>
      </div>
    </div>
    <h3>
      {{'Based on deaths'|translate:'app'|escape}}
    </h3>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
        <div class="graph stat-wp" data-base="death" data-scale="no">
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-6 col-xl-6">
        <div class="graph stat-wp" data-base="death" data-scale="yes">
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
            var kdKey = $graph.attr('data-base');
            var scale = $graph.attr('data-scale') === 'yes';
            var json = window.kddata;
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
    <h3>
      {{'Winning Percentage based on K/D'|translate:'app'|escape}}
    </h3>
    <p>
      {{$_filter = [
          "weapon" => $weapon->key
        ]}}
      <a href="{{url route="entire/kd-win" filter=$_filter}}#{{$rule->key|escape:"url"|escape}}">
        {{'Winning Percentage based on K/D'|translate:'app'|escape}}
      </a>
    </p>
    <h3>
      {{'Stage'|translate:'app'|escape}}
    </h3>
    {{use class="app\components\widgets\WinLoseLegend"}}
    {{WinLoseLegend::widget()}}
    <div class="row">
      {{foreach $maps as $map}}
        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
          <h4>{{$map.name}}</h4>
          <div class="graph stat-map-wp" data-map="{{$map.key|escape}}">
          </div>
        </div>
      {{/foreach}}
    </div>
    {{\jp3cki\yii2\flot\FlotPieAsset::register($this)|@void}}
    {{registerJs}}
      (function($){
        "use strict";
        function update() {
          var $graphs = $('.graph.stat-map-wp');
          $graphs.height($graphs.width());
          $graphs.each(function(){
            var $graph = $(this);
            var mapKey = $graph.attr('data-map');
            var jsonData = window.mapdata[mapKey];
            if (!jsonData) {
              return;
            }
            var data = [
              {
                label: "Won",
                data: jsonData.win
              },
              {
                label: "Lost",
                data: jsonData.battle - jsonData.win
              }
            ];
            $.plot($graph, data, {
              series: {
                pie: {
                  show: true,
                  radius: 1,
                  label: {
                    show: "auto",
                    radius: .618,
                    formatter: function(label, slice) {
                      return $('<div>').append(
                        $('<div>').css({
                          'fontSize': '1em',
                          'lineHeight': '1.1em',
                          'textAlign': 'center',
                          'padding': '2px',
                          'color': '#fff',
                          'textShadow': '0px 0px 3px #000',
                        }).append(
                          slice.data[0][1] + ' / ' + Math.round(slice.data[0][1] / (slice.percent / 100))
                        ).append(
                          $('<br>')
                        ).append(
                          slice.percent.toFixed(1) + '%'
                        )
                      ).html();
                    },
                  },
                },
              },
              legend: {
                show: false
              },
              colors: [
                window.colorScheme.win,
                window.colorScheme.lose,
              ]
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

    <h3 id="vs-weapon">ブキ別対戦成績</h3>
    {{use class="app\models\SummarizedWeaponVsWeapon"}}
    {{$_list = SummarizedWeaponVsWeapon::find($weapon->id, $rule->id)}}
    <div class="table-responsive">
      <table class="table table-striped table-condensed">
        <thead>
          <tr>
            <th>{{'Weapon'|translate:'app'|escape}}</th>
            <th>{{'Battles'|translate:'app'|escape}}</th>
            <th class="vs-weapon-bar">{{'Win %'|translate:'app'|escape}}</th>
          </tr>
        </thead>
        <tbody>
          {{foreach $_list as $_row}}
            <tr>
              <td>
                <span title="{{*
                    *}}{{'Sub:'|translate:'app'|escape}}{{$_row->rhsWeapon->subweapon->name|default:'?'|translate:'app-subweapon'|escape}} / {{*
                    *}}{{'Special:'|translate:'app'|escape}}{{$_row->rhsWeapon->special->name|default:'?'|translate:'app-special'|escape}}" class="auto-tooltip">
                  {{$_row->rhsWeapon->name|default:'?'|translate:'app-weapon'|escape}}
                </span>
              </td>
              <td class="text-right">
                {{$_row->battle_count|number_format|escape}}
              </td>
              <td class="vs-weapon-bar">
                {{if !$_row->winPct|is_nan}}
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width:{{$_row->winPct|escape}}%;">
                      {{$_row->winPct|number_format:2|escape}}%
                    </div>
                  </div>
                {{/if}}
              </td>
            </tr>
          {{foreachelse}}
            <tr>
              <td colspan="3">
                {{'There are no data.'|translate:'app'|escape}}
              </td>
            </tr>
          {{/foreach}}
        </tbody>
      </table>
      {{registerCss}}
        .vs-weapon-bar{min-width:200px}
        .progress{margin-bottom:0}
      {{/registerCss}}
    </div>
  </div>
  {{registerJs}}
    $(window).resize();
  {{/registerJs}}
{{/strip}}
