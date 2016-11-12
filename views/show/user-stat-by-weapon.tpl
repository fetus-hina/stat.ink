{{strip}}
  {{set layout="main.tpl"}}
  {{use class="yii\helpers\Url"}}

  {{$title = "{0}'s Battle Stats (by Weapon)"|translate:'app':$user->name}}
  {{set title="{{$app->name}} | {{$title}}"}}

  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->userIcon->absUrl|default:$user->jdenticonPngUrl])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{SnsWidget}}

    {{\app\assets\JqueryStupidTableAsset::register($this)|@void}}
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

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <table class="table table-striped table-sortable">
          <thead>
            <tr>
              <th data-sort="string">{{'Weapon'|translate:'app'|escape}}</th>
              <th data-sort="int">{{'Battles'|translate:'app'|escape}}&#32;<span class="arrow fa fa-angle-down"></span></th>
              <th data-sort="float">{{'Win %'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Kills'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg Deaths'|translate:'app'|escape}}</th>
              <th data-sort="float">{{'Avg KR'|translate:'app'|escape}}</th>
            </tr>
          </thead>
          <tbody>
            {{foreach $list as $i => $row}}
              <tr>
                <td>
                  {{$row.weapon_name|translate:'app-weapon'|escape}}
                </td>

                <td class="text-right" data-sort-value="{{$row.battles|escape}}">
                  {{$_params = array_merge($filter->toQueryParams(), ['0' => 'show/user'])}}
                  {{$_params.filter.weapon = $row.weapon_key}}
                  <a href="{{Url::to($_params)|escape}}">
                    {{$row.battles|number_format|escape}}
                  </a>
                </td>

                {{$_v = 100 * $row.battles_win / $row.battles}}
                <td class="text-right" data-sort-value="{{$_v|escape}}">
                  {{$_v|number_format:2|escape}} %
                </td>

                {{if $row.kd_available > 0}}
                  {{$_k = $row.kills / $row.kd_available}}
                  {{$_d = $row.deaths / $row.kd_available}}
                  <td class="text-right" data-sort-value="{{$_k|escape}}">
                    {{$_k|number_format:2|escape}}
                  </td>
                  <td class="text-right" data-sort-value="{{$_d|escape}}">
                    {{$_d|number_format:2|escape}}
                  </td>
                  {{if $_d == 0}}
                    {{if $_k == 0}}
                      <td class="text-center" data-sort-value="-1">
                        -
                      </td>
                    {{else}}
                      <td class="text-right" data-sort-value="99.99">
                        99.99
                      </td>
                    {{/if}}
                  {{else}}
                    {{$_v = $_k / $_d}}
                    <td class="text-right" data-sort-value="{{$_v|escape}}">
                      {{$_v|number_format:2|escape}}
                    </td>
                  {{/if}}
                {{else}}
                  <td class="text-center" data-sort-value="-1">
                    -
                  </td>
                  <td class="text-center" data-sort-value="-1">
                    -
                  </td>
                  <td class="text-center" data-sort-value="-1">
                    -
                  </td>
                {{/if}}
              </tr>
            {{foreachelse}}
              <tr>
                <td>{{'There are no data.'|translate:'app'|escape}}</td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{BattleFilterWidget route="show/user-stat-by-weapon" screen_name=$user->screen_name filter=$filter action="summarize" weapon=false result=false}}
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
