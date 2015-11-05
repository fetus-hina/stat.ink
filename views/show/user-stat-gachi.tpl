{{strip}}
  {{set layout="main.tpl"}}

  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "{0}'s Battle Stat (Ranked Battle)"|translate:'app':$name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  {{\app\assets\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <h2 id="exp">
          {{'Rank'|translate:'app'|escape}}
        </h2>
        <p>
          {{'Excluded: Private Battles and Squad Battles(when Rank S, S+)'|translate:'app'|escape}}
        </p>
        <div class="graph" id="stat-rank" data-json="{{$recentRank|json_encode|escape}}">
        </div>

        <hr>

        <h2 id="wp">
          {{'Winning Percentage'|translate:'app'|escape}}
        </h2>
        <p>
          {{'Excluded: Private Battles'|translate:'app'|escape}}
        </p>
        <div class="graph" id="stat-wp" data-json="{{$recentWP|json_encode|escape}}">
        </div>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{include file="@app/views/includes/ad.tpl"}}
    </div>
  </div>
{{/strip}}
{{registerCss}}
  .stat-rank{height:300px}
{{/registerCss}}
{{registerJs}}
(function($) {
  var $graphs = $('.graph');
  
  function drawRankGraph() {
    var $graph = $graphs.filter('#stat-rank');
    var json = JSON.parse($graph.attr('data-json'));
    var data = [
      {
        label: "{{'Rank'|translate:'app'|escape:'javascript'}}",
        data: json.map(
          function(v) {
            return [v.index, v.exp];
          },
          json
        )
      },
      {
        label: "{{'Moving Avg. ({0} Battles)'|translate:'app':10|escape}}",
        data: json.map(
          function(v) {
            return [v.index, v.movingAvg];
          },
          json
        )
      }
    ];
    $.plot($graph, data, {
      xaxis: {
        minTickSize: 1,
        tickFormatter: function (v) {
          return ~~v;
        }
      },
      yaxis: {
        minTickSize: 10,
        tickFormatter: function (v) {
          if (v >= 1100) {
            return v >= 1150 ? '' : 'S+ 99';
          }

          var rank = Math.floor(v / 100);
          var exp = v % 100;
          var ranks = ['C-', 'C', 'C+', 'B-', 'B', 'B+', 'A-', 'A', 'A+', 'S', 'S+'];
          return ranks[rank] + " " + exp;
        }
      },
      legend: {
        position: "nw"
      }
    });
  }

  function drawWPGraph() {
    var $graph = $graphs.filter('#stat-wp');
    var json = JSON.parse($graph.attr('data-json'));
    var data = [
      {
        label: "{{'WP (Totally)'|translate:'app'|escape:'javascript'}}",
        data: json.map(
          function(v) {
            return [v.index, v.totalWP];
          },
          json
        )
      },
      {
        label: "{{'WP ({0} Battles)'|translate:'app':20|escape}}",
        data: json.map(
          function(v) {
            return [v.index, v.movingWP];
          },
          json
        )
      }
    ];
    $.plot($graph, data, {
      xaxis: {
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
      $graphs.height($graphs.width() * 9 / 16);
      drawRankGraph();
      drawWPGraph();
    }, 33);
  }).resize();
})(jQuery);
{{/registerJs}}
