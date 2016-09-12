{{strip}}
  {{set layout="main.tpl"}}

  {{$title = 'Weapons'|translate:'app'}}
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

    <h2>
      {{'Weapons'|translate:'app'|escape}}
    </h2>
    <p>
      {{'Excluded: The uploader, All players (Private Battle), Uploader\'s teammates (Squad Battle or Splatfest Battle)'|translate:'app'|escape}}
    </p>
    <p>
      {{'* This exclusion is in attempt to minimize overcounting in weapon usage statistics.'|translate:'app'|escape}}
    </p>
    {{\app\assets\JqueryStupidTableAsset::register($this)|@void}}
    {{foreach $entire as $rule}}
      {{if !$rule@first}} | {{/if}}
      <a href="#weapon-{{$rule->key|escape}}">{{$rule->name|escape}}</a>
    {{/foreach}}
    
    {{\jp3cki\yii2\flot\FlotAsset::register($this)|@void}}
    {{\jp3cki\yii2\flot\FlotTimeAsset::register($this)|@void}}
    {{\jp3cki\yii2\flot\FlotStackAsset::register($this)|@void}}
    {{registerCss}}.graph{height:300px}{{/registerCss}}
    <h3 id="trends">
      {{'Trends'|translate:'app'|escape}}
    </h3>
    <p>
      <a href="{{url route="entire/weapons-use"}}" class="btn btn-default">
        <span class="fa fa-exchange fa-fw"></span>&#32;{{'Compare number of uses'|translate:'app'|escape}}
      </a>
    </p>
    <div id="graph-trends-legends"></div>
    <div id="graph-trends" class="graph">
    </div>
    <p class="text-right">
      <label>
        <input type="checkbox" id="stack-trends" value="1" checked>&#32;{{'Stack'|translate:'app'|escape}}
      </label>
    </p>
    <script id="trends-json" type="application/json">
      {{$uses|@json_encode}}
    </script>
    {{registerJs}}
      (function($){
        "use strict";
        var stack = true;
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
          var $graphs = $('#graph-trends');
          var json = JSON.parse($('#trends-json').text());
          var data = [];
          for (var i = 0; i < json[0].weapons.length; ++i) {
            var weapon = json[0].weapons[i];
            data.push({
              label: json[0].weapons[i].name,
              data: json.map(function(week) {
                return [
                  date2unixTime(week.date),
                  week.weapons[i].pct
                ];
              }),
            });
          }
          if (stack) {
            data.push({
              label: "{{'Others'|translate:'app'|escape:javascript}}",
              data: json.map(function(week){
                return [
                  date2unixTime(week.date),
                  week.others_pct
                ];
              }),
              color: '#cccccc'
            });
          }
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
                max: stack ? 100 : undefined,
                tickFormatter:function(v){
                  return v.toFixed(1)+"%";
                }
              },
              series: {
                stack: stack,
                points: {
                  show: !stack,
                },
                lines: {
                  show: true,
                  fill: stack,
                  steps: false,
                }
              },
              legend: {
                sorted: stack ? "reverse" : false,
                position: "nw",
                container: $('#graph-trends-legends'),
                noColumns: (function() {
                  var width = $(window).width();
                  if (width < 768) {
                    return 1;
                  } else if (width < 992) {
                    return 2;
                  } else if (width < 1200) {
                    return 4;
                  } else {
                    return 5;
                  }
                })()
              }
            });
            window.setTimeout(function () {
              var $labels = $('td.legendLabel', $('#graph-trends-legends'));
              $labels.width(
                Math.max.apply(null, $labels.map(function () {
                  return $(this).width('').width();
                })) + 12
              );
            }, 1);
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
        }).resize();

        $('#stack-trends').click(function () {
          stack = !!$(this).prop('checked');
          $(window).resize();
        });
      })(jQuery);
    {{/registerJs}}

    {{foreach $entire as $rule}}
      {{if $rule->data->battle_count > 0}}
        <h3 id="weapon-{{$rule->key|escape}}">
          {{$rule->name|escape}}
        </h3>
        <p>
          {{'Battles:'|translate:'app'|escape}} {{$rule->data->battle_count|number_format|escape}},&#32;
          {{'Players:'|translate:'app'|escape}} {{$rule->data->player_count|number_format|escape}}
        </p>
        <table class="table table-striped table-condensed table-sortable">
          <thead>
            <tr>
              <th data-sort="string">{{'Weapon'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Players'|translate:'app'|escape}} <span class="arrow fa fa-angle-down"></span></th>
              <th data-sort="float">{{'Avg Kills'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Deaths'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
              {{if $rule->key === 'nawabari'}}
                <th data-sort="float">{{'Avg Inked'|translate:'app'|escape}}</th>
              {{/if}}
              <th data-sort="float">{{'Win %'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $rule->data->weapons as $weapon}}
              <tr class="weapon">
                <td>
                  <a href="{{url route="entire/weapon" weapon=$weapon->key rule=$rule->key}}">
                    <span title="{{'Sub:'|translate:'app'|escape}}{{$weapon->subweapon->name|escape}} / {{'Special:'|translate:'app'|escape}}{{$weapon->special->name|escape}}" class="auto-tooltip">
                      {{$weapon->name|escape}}
                    </span>
                  </a>
                </td>
                <td class="players" title="{{if $weapon->count > 0}}{{($rule->data->player_count*100/$weapon->count)|string_format:'%.2f%%'|escape}}{{/if}}" data-sort-value="{{$weapon->count|escape}}">
                  {{if $rule->data->player_count > 0}}
                    <span class="auto-tooltip" title="{{($weapon->count*100/$rule->data->player_count)|string_format:'%.2f%%'|escape}}">
                      {{$weapon->count|number_format|escape}}
                    </span>
                  {{else}}
                    0
                  {{/if}}
                </td>
                <td class="kill" data-sort-value="{{$weapon->avg_kill|escape}}">{{$weapon->avg_kill|string_format:'%.2f'|escape}}</td>
                <td class="death" data-sort-value="{{$weapon->avg_death|escape}}">{{$weapon->avg_death|string_format:'%.2f'|escape}}</td>
                {{if $weapon->avg_death == 0}}
                  {{if $weapon->avg_kill > 0}}
                    {{$kr = 99.99}}
                  {{else}}
                    {{$kr = null}}
                  {{/if}}
                {{else}}
                  {{$kr = $weapon->avg_kill / $weapon->avg_death}}
                {{/if}}
                <td data-sort-value="{{$kr|escape}}">
                  {{if $kr !== null}}
                    {{$kr|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
                {{if $rule->key === 'nawabari'}}
                  <td data-sort-value="{{if $weapon->avg_inked === null}}-1{{else}}{{$weapon->avg_inked|escape}}{{/if}}">
                    {{$weapon->avg_inked|string_format:'%.1f'|escape}}
                  </td>
                {{/if}}
                <td data-sort-value="{{$weapon->wp|escape}}">
                  {{$weapon->wp|string_format:'%.2f%%'|escape}}
                </td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>

        <table class="table table-striped table-condensed table-sortable" id="sub-{{$rule->key|escape}}">
          <thead>
            <tr>
              <th data-sort="string">{{'Sub Weapon'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Players'|translate:'app'|escape}} <span class="arrow fa fa-angle-down"></span></th>
              <th data-sort="float">{{'Avg Kills'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Deaths'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Win %'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Encounter Ratio'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $rule->sub as $weapon}}
              <tr class="weapon">
                <td>
                  {{$weapon->name|escape}}
                </td>
                <td class="players" title="{{if $weapon->count > 0}}{{($rule->data->player_count*100/$weapon->count)|string_format:'%.2f%%'|escape}}{{/if}}" data-sort-value="{{$weapon->count|escape}}">
                  {{if $rule->data->player_count > 0}}
                    <span class="auto-tooltip" title="{{($weapon->count*100/$rule->data->player_count)|string_format:'%.2f%%'|escape}}">
                      {{$weapon->count|number_format|escape}}
                    </span>
                  {{else}}
                    0
                  {{/if}}
                </td>
                <td class="kill" data-sort-value="{{$weapon->avg_kill|escape}}">{{$weapon->avg_kill|string_format:'%.2f'|escape}}</td>
                <td class="death" data-sort-value="{{$weapon->avg_death|escape}}">{{$weapon->avg_death|string_format:'%.2f'|escape}}</td>
                {{if $weapon->avg_death == 0}}
                  {{if $weapon->avg_kill > 0}}
                    {{$kr = 99.99}}
                  {{else}}
                    {{$kr = null}}
                  {{/if}}
                {{else}}
                  {{$kr = $weapon->avg_kill / $weapon->avg_death}}
                {{/if}}
                <td data-sort-value="{{$kr|escape}}">
                  {{if $kr !== null}}
                    {{$kr|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
                <td data-sort-value="{{$weapon->wp|escape}}">
                  {{$weapon->wp|string_format:'%.2f%%'|escape}}
                </td>
                <td data-sort-value="{{$weapon->encounter_4|escape}}">
                  {{$weapon->encounter_4|string_format:'%.2f%%'|escape}}
                </td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>

        <table class="table table-striped table-condensed table-sortable" id="special-{{$rule->key|escape}}">
          <thead>
            <tr>
              <th data-sort="string">{{'Special'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Players'|translate:'app'|escape}} <span class="arrow fa fa-angle-down"></span></th>
              <th data-sort="float">{{'Avg Kills'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Deaths'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Win %'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Encounter Ratio'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $rule->special as $weapon}}
              <tr class="weapon">
                <td>
                  {{$weapon->name|escape}}
                </td>
                <td class="players" title="{{if $weapon->count > 0}}{{($rule->data->player_count*100/$weapon->count)|string_format:'%.2f%%'|escape}}{{/if}}" data-sort-value="{{$weapon->count|escape}}">
                  {{if $rule->data->player_count > 0}}
                    <span class="auto-tooltip" title="{{($weapon->count*100/$rule->data->player_count)|string_format:'%.2f%%'|escape}}">
                      {{$weapon->count|number_format|escape}}
                    </span>
                  {{else}}
                    0
                  {{/if}}
                </td>
                <td class="kill" data-sort-value="{{$weapon->avg_kill|escape}}">{{$weapon->avg_kill|string_format:'%.2f'|escape}}</td>
                <td class="death" data-sort-value="{{$weapon->avg_death|escape}}">{{$weapon->avg_death|string_format:'%.2f'|escape}}</td>
                {{if $weapon->avg_death == 0}}
                  {{if $weapon->avg_kill > 0}}
                    {{$kr = 99.99}}
                  {{else}}
                    {{$kr = null}}
                  {{/if}}
                {{else}}
                  {{$kr = $weapon->avg_kill / $weapon->avg_death}}
                {{/if}}
                <td data-sort-value="{{$kr|escape}}">
                  {{if $kr !== null}}
                    {{$kr|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
                <td data-sort-value="{{$weapon->wp|escape}}">
                  {{$weapon->wp|string_format:'%.2f%%'|escape}}
                </td>
                <td data-sort-value="{{$weapon->encounter_4|escape}}">
                  {{$weapon->encounter_4|string_format:'%.2f%%'|escape}}
                </td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      {{/if}}
    {{/foreach}}
    {{registerJs}}
      (function(){
        $('.table-sortable')
          .stupidtable()
          .on("aftertablesort",function(event,data){
            var th = $(this).find("th");
            th.find(".arrow").remove();
            var dir = $.fn.stupidtable.dir;
            var arrow = data.direction === dir.ASC ? "fa-angle-up" : "fa-angle-down";
            th.eq(data.column)
              .append(' ')
              .append(
                $('<span/>').addClass('arrow fa').addClass(arrow)
              );
          });
      })();
    {{/registerJs}}

    <h2>
      {{'Favorite Weapons of This Site Member'|translate:'app'|escape}}
    </h2>
    <table class="table table-striped table-condensed">
      <tbody>
        {{$_max = 0}}
        {{foreach $users as $_w}}
          <tr>
            <td class="text-right" style="width:15em">
              {{$_w->weapon->name|default:'?'|translate:'app-weapon'|escape}}
            </td>
            <td>
              {{if $_max < $_w->user_count}}
                {{$_max = $_w->user_count}}
              {{/if}}
              {{if $_max > 0}}
                {{registerCss}}.progress{margin-bottom:0}{{/registerCss}}
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width:{{($_w->user_count*100/$_max)|escape}}%;">
                    {{$_w->user_count|number_format|escape}}
                  </div>
                </div>
              {{/if}}
            </td>
          </tr>
        {{/foreach}}
      </tbody>
    </table>
  </div>
{{/strip}}
