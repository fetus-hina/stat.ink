{{strip}}
  {{set layout="main.tpl"}}

  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "{0}'s Battle Stats (Turf War)"|translate:'app':$name}}
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
        <h2>
          {{'Turf Inked'|translate:'app'|escape}}
        </h2>
        <p>
          {{'Excluded: Private Battles'|translate:'app'|escape}}
        </p>
        <p>
          {{foreach $inked as $map}}
            {{if !$map@first}}
              &#32;|&#32;
            {{/if}}
            <a href="#inked-{{$map->key|escape}}">
              {{$map->name|escape}}
            </a>
          {{/foreach}}
        </p>
        <script>
          window._inked = {{$inked|json_encode}};
        </script>
        {{foreach $inked as $map}}
          <h3 id="inked-{{$map->key|escape}}">
            {{$filter = [
                'rule' => 'nawabari',
                'map' => $map->key
              ]}}
            <a href="{{url route="show/user" screen_name=$user->screen_name filter=$filter}}">
              {{$map->name|escape}}
            </a> {{if $map->area}}({{$map->area|number_format|escape}}p){{/if}}
          </h3>
          {{if $map->avgInked !== null}}
            <p>
              {{'Average:'|translate:'app'|escape}} {{$map->avgInked|string_format:'%.1f'|escape}}p
              {{if $map->area}}
                , {{($map->avgInked*100/$map->area)|string_format:'%.1f%%'|escape}}
              {{/if}}
            </p>
          {{/if}}
          <div class="graph stat-inked" data-map="{{$map->key|escape}}"></div>
        {{/foreach}}

        <hr>

        <h2 id="wp">
          {{'Winning Percentage'|translate:'app'|escape}}
        </h2>
        <p>
          {{'Excluded: Private Battles'|translate:'app'|escape}}
        </p>
        <script>
          window._wpData = {{$wp|json_encode}};
        </script>
        <div id="stat-wp-legend"></div>
        <div class="graph stat-wp"></div>
        <div class="graph stat-wp" data-limit="200"></div>
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
//<script>
(function($) {
  var $graphs = $('.graph');
  var colorLock = window.colorLock;
  var colorScheme = {
    graph1:   colorLock ? window.colorScheme.graph1 :  window.colorScheme._accent.orange,
    moving1:  colorLock ? window.colorScheme.moving1 : 'rgba(64,237,64,.5)',
    moving2:  colorLock ? window.colorScheme.moving2 : 'rgba(148,64,237,.5)'
  };
  
  function drawInkedGraph(json_) {
    var $graph_ = $graphs.filter('.stat-inked');

    $graph_.each(function () {
      var $graph = $(this);
      var mapKey = $graph.attr('data-map');
      var json = json_[mapKey];

      var data = [
        {
          label: "{{'Turf Inked'|translate:'app'|escape:'javascript'}}",
          data: json.battles.map(function (v) {
            return [v.index, v.inked];
          }),
          color: colorScheme.graph1
        }
      ];

      var max = Math.max.apply(null, json.battles.map(function (v) {
        return v.inked;
      }));

      $.plot($graph, data, {
        xaxis: {
          minTickSize: 1,
          tickFormatter: function (v) {
            return ~~v;
          }
        },
        yaxis: {
          minTickSize: 100,
          min: 0,
          max: (function() {
            if (json.area === null) {
              return null;
            }
            if (json.area > max) {
              return json.area;
            }
            return null;
          })(),
          tickFormatter: function (v) {
            if (json.area == null) {
              return v;
            }
            return v + 'p (' + (v * 100 / json.area).toFixed(1) + '%)';
          }
        },
        legend: {
          position: 'nw'
        },
        series: {
          lines: {
            show: true, fill: true
          }
        }
      });
    });
  }

  function drawWPGraph(json) {
    var $graph_ = $graphs.filter('.stat-wp');

    var data = [
      {
        label: "{{'Winning Percentage'|translate:'app'|escape:'javascript'}}",
        data: json.map(function(v) {
          return [v.index, v.totalWP];
        }),
        color: colorScheme.graph1
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
      drawInkedGraph(window._inked);
      drawWPGraph(window._wpData);
    }, 33);
  }).resize();

  $('#show-rank-moving-avg').click(function () {
    $(window).resize();
  });
})(jQuery);
{{/registerJs}}
