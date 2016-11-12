{{strip}}
  {{set layout="main.tpl"}}

  {{$title = "{0}'s Battle Stats (vs. Weapon)"|translate:'app':$user->name}}
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

{{\app\assets\JqueryStupidTableAsset::register($this)|@void}}
{{registerJs}}
(function($){
  $('.table-sortable')
    .stupidtable()
    .on("aftertablesort",function(event,data){
      var th = $(this).find("th");
      th.find(".arrow").remove();
      var dir = $.fn.stupidtable.dir;
      var arrow = data.direction === dir.ASC ? "fa-angle-up" : "fa-angle-down";
      th.eq(data.column)
        .append(' ')
        .append($('<span/>').addClass('arrow fa').addClass(arrow));
      });
})(jQuery);
{{/registerJs}}

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <table class="table table-striped table-sortable">
          <thead>
            <tr>
              <th data-sort="string">{{'Enemy Weapon'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Battles'|translate:'app'|escape}}</th>
              <th data-sort="float">
                {{'Win %'|translate:'app'|escape}} <span class="arrow fa fa-angle-down"></span>
              </th>
              <th data-sort="int">{{'Deaths'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Deaths Per Battle'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $data as $i => $row}}
              <tr>
                <td data-sort-value="{{$row.weapon_name|escape}}">
                  {{$row.weapon_name|escape}}
                </td>
                <td data-sort-value="{{$row.battles|default:"-1"|escape}}">
                  {{if $row.battles|default:0}}
                    {{$row.battles|number_format|escape}}
                  {{/if}}
                </td>
                <td data-sort-value="{{$row.win_pct|default:"-1"|escape}}">
                  {{if $row.win_pct|default:0}}
                    {{$row.win_pct|number_format:2|escape}} %
                  {{/if}}
                </td>
                <td data-sort-value="{{$row.deaths|default:"-1"|escape}}">
                  {{if $row.deaths|default:9999 != 9999}}
                    {{$row.deaths|number_format|escape}}
                  {{/if}}
                </td>
                <td data-sort-value="{{$row.deaths_per_game|default:"-1"|escape}}">
                  {{if $row.deaths_per_game|default:9999 != 9999}}
                    {{$row.deaths_per_game|number_format:3|escape}}
                  {{/if}}
                </td>
              </tr>
            {{foreachelse}}
              <tr>
                <td colspan="5">{{'There are no data.'|translate:'app'|escape}}</td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{BattleFilterWidget route="show/user-stat-vs-weapon" screen_name=$user->screen_name filter=$filter action="summarize" result=false}}
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
