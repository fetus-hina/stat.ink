{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Battles and Users'|translate:'app'}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    {{\app\assets\FlotAsset::register($this)|@void}}
    {{\app\assets\FlotTimeAsset::register($this)|@void}}
    <div id="graph" data-data="{{$posts|json_encode|escape}}" data-label-battle="{{'Battle Count'|translate:'app'|escape}}" data-label-user="{{'User Count'|translate:'app'|escape}}">
    </div>

    <h2>
      {{'User Agents in 24H'|translate:'app'|escape}}
    </h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>{{'Battle Count'|translate:'app'|escape}}</th>
          <th>{{'User Count'|translate:'app'|escape}}</th>
          <th>{{'User Agent'|translate:'app'|escape}}</th>
        </tr>
      </thead>
      <tbody>
        {{foreach $agents as $agent}}
          <tr>
            <td class="text-right">{{$agent.battle|number_format|escape}}</td>
            <td class="text-right">{{$agent.user|number_format|escape}}</td>
            <td>{{$agent.agent_name|escape}} / {{$agent.agent_version|escape}}</td>
          </tr>
        {{/foreach}}
      </tbody>
    </table>
  </div>
{{/strip}}
{{registerCss}}
#graph{height:300px}
{{/registerCss}}
{{registerJs}}{{literal}}
(function($) {
  var $graph = $('#graph');

  function drawGraph() {
    function dateToUnixTime(d) {
      return (new Date(d + 'T00:00:00Z')).getTime();
    }
    function formatDate(date) {
      function zero(n) {
        n = n + "";
        return (n.length == 1) ? "0" + n : n;
      }
      return date.getUTCFullYear() + "-" + zero(date.getUTCMonth() + 1) + "-" + zero(date.getUTCDate());
    }

    var json = JSON.parse($graph.attr('data-data'));
    var data = [
      {
        label:$graph.attr('data-label-battle'),
        data:json.map(function(v){return[dateToUnixTime(v.date),v.battle]}),
        bars:{
          show:true,
          align: "center",
          barWidth: 86400*900,
          lineWidth:1
        }
      },
      {
        label:$graph.attr('data-label-user'),
        data:json.map(function(v){return[dateToUnixTime(v.date),v.user]}),
        yaxis:2
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
      y2axis: {
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
      $graph.height(
        $graph.width() * 9 /16
      );
      drawGraph();
    }, 33);
  }).resize();
})(jQuery);
{{/literal}}{{/registerJs}}
