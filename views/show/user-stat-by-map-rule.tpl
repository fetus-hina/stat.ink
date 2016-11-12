{{strip}}
  {{set layout="main.tpl"}}

  {{$title = "{0}'s Battle Stats (by Mode and Stage)"|translate:'app':$user->name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->userIcon->absUrl|default:$user->jdenticonPngUrl])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  {{\jp3cki\yii2\flot\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{SnsWidget}}

    <p>
      <a href="{{url route="show/user-stat-by-map-rule-detail" screen_name=$user->screen_name}}" class="btn btn-success">
        <span class="fa fa-newspaper-o left"></span>
        {{'Details'|translate:'app'|escape}}
        <span class="fa fa-angle-double-right right"></span>
      </a>
    </p>

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 table-responsive table-responsive-force">
        <table class="table table-condensed graph-container">
          <thead>
            <tr>
              <th>
                {{WinLoseLegend}}
              </th>
              {{foreach $ruleNames as $ruleKey => $ruleName}}
                <th>
                  {{$_filter = ['rule' => $ruleKey]}}
                  <a href="{{url route="show/user" screen_name=$user->screen_name filter=$_filter}}">
                    {{$ruleName|escape}}
                  </a>
                </th>
              {{/foreach}}
            </tr>
          </thead>
          <tbody>
            {{foreach $mapNames as $mapKey => $mapName}}
              <tr>
                <th>
                  {{$_filter = ['map' => $mapKey]}}
                  <a href="{{url route="show/user" screen_name=$user->screen_name filter=$_filter}}">
                    {{$mapName|escape}}
                  </a>
                </th>
                {{foreach $ruleNames as $ruleKey => $ruleName}}
                  <td>
                    {{$_filter = ['rule' => $ruleKey, 'map' => $mapKey]}}
                    <div class="pie-flot-container" data-json="{{$data[$mapKey][$ruleKey]|json_encode|escape}}" data-clink-href="{{url route="show/user" screen_name=$user->screen_name filter=$_filter}}">
                    </div>
                  </td>
                {{/foreach}}
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{BattleFilterWidget route="show/user-stat-by-map-rule" screen_name=$user->screen_name filter=$filter action="summarize" rule=false map=false result=false}}
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerJs}}
window.statByMapRule();
(function($){
  var $th = $('.graph-container thead tr:nth-child(1) th');
  $th.css({
    width: '20%',
    'min-width': '150px'
  });
})(jQuery);
{{/registerJs}}
{{registerCss}}.pie-flot-container{height:200px}.pie-flot-container .error{display:none}{{/registerCss}}
