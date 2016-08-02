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

    {{AdWidget}}
    {{SnsWidget}}

    {{\jp3cki\yii2\flot\FlotAsset::register($this)|@void}}
    {{\jp3cki\yii2\flot\FlotTimeAsset::register($this)|@void}}
    <div id="graph" data-data="{{$posts|json_encode|escape}}" data-label-battle="{{'Battles'|translate:'app'|escape}}" data-label-user="{{'Users'|translate:'app'|escape}}">
    </div>
    {{registerCss}}#graph{height:300px;margin-bottom:10px}{{/registerCss}}
    {{if $combineds}}
      <p>
        {{foreach $combineds as $name}}
          {{if !$name@first}} | {{/if}}
          {{$b32 = $name|base32_encode:false|strtolower}}
          <a href="{{url route="entire/combined-agent" b32name=$b32}}">
            {{$name|mb_strimwidth:0:20:'…':'UTF-8'|escape}}
          </a>
        {{/foreach}}
      </p>
    {{/if}}
    {{if $agentNames}}
      <p>
        {{foreach $agentNames as $name}}
          {{if !$name@first}} | {{/if}}
          {{$b32 = $name|base32_encode:false|strtolower}}
          <a href="{{url route="entire/agent" b32name=$b32}}">
            {{$name|mb_strimwidth:0:20:'…':'UTF-8'|escape}}
          </a>
        {{/foreach}}
      </p>
    {{/if}}

    <h2>
      {{'User Agents in last 24 hours'|translate:'app'|escape}}
    </h2>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>{{'Battles'|translate:'app'|escape}}</th>
          <th>{{'Users'|translate:'app'|escape}}</th>
          <th>{{'User Agent'|translate:'app'|escape}}</th>
        </tr>
      </thead>
      <tbody>
        {{foreach $agents as $agent}}
          <tr>
            <td class="text-right">{{$agent.battle|number_format|escape}}</td>
            <td class="text-right">{{$agent.user|number_format|escape}}</td>
            <td>
              {{if $agent.agent_prod_url}}<a href="{{$agent.agent_prod_url|escape}}" target="_blank" rel="nofollow">{{/if}}
              {{$agent.agent_name|escape}}
              {{if $agent.agent_prod_url}}</a>{{/if}}
              &#32;/&#32;
              {{if $agent.agent_rev_url}}<a href="{{$agent.agent_rev_url|escape}}" target="_blank" rel="nofollow">{{/if}}
              {{$agent.agent_version|escape}}
              {{if $agent.agent_rev_url}}</a>{{/if}}

              {{if $agent.agent_is_old}}
                &#32;<span class="old-ikalog">
                  {{'[Outdated]'|translate:'app'|escape}}
                </span>
                {{registerCss}}
                  .old-ikalog {
                    color: #f00;
                    font-weight: bold;
                  }
                {{/registerCss}}
              {{/if}}
            </td>
          </tr>
        {{/foreach}}
      </tbody>
    </table>
  </div>
{{/strip}}
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
          barWidth: 86400*1000*.8,
          lineWidth:1
        },
        color:window.colorScheme.graph1
      },
      {
        label:$graph.attr('data-label-user'),
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
      $graph.height(
        $graph.width() * 9 /16
      );
      drawGraph();
    }, 33);
  }).resize();
})(jQuery);
{{/literal}}{{/registerJs}}
