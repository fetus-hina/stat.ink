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

  <div class="container">
    <h1>
      {{$title|escape}}
    </h1>

    {{SnsWidget}}

    {{$_am = $app->getAssetManager()}}
    {{$_appAsset = $_am->getBundle('app\assets\AppAsset')}}
    {{$_url = $_am->getAssetUrl($_appAsset, 'user-stat-by-map-rule-detail.css')}}
    {{registerCssFile url=$_url depends='app\assets\AppAsset'}}

    <p>
      <a href="{{url route="show/user-stat-by-map-rule" screen_name=$user->screen_name}}" class="btn btn-default">
        <span class="fa fa-angle-double-left left"></span>
        {{'Back'|translate:'app'|escape}}
      </a>
      &#32;
      <a href="#filter-form" class="visible-xs-inline btn btn-info">
        <span class="fa fa-search left"></span>
        {{'Search'|translate:'app'|escape}}
      </a>
    </p>

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 table-responsive table-responsive-force">
        <table class="table table-condensed graph-container">
          <thead>
            <tr>
              <th></th>
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
                <th class="map-name">
                  {{$_filter = ['map' => $mapKey]}}
                  <a href="{{url route="show/user" screen_name=$user->screen_name filter=$_filter}}">
                    <span class="visible-lg-inline">{{$mapName->name|escape}}</span>
                    <span class="hidden-lg" aria-hidden="true">{{$mapName->short|escape}}</span>
                  </a>
                </th>
                {{foreach $ruleNames as $ruleKey => $ruleName}}
                  <td class="detail-data">
                    {{$_data = $data[$mapKey][$ruleKey]}}
                    {{'Win'|translate:'app'|escape}}:&#32;
                      <span class="positive">{{$_data->win|number_format|escape}}</span>
                      {{if $_data->win_ko > 0 || $_data->win_to > 0}}
                        &#32;({{'KO'|translate:'app'|escape}}: {{($_data->win_ko*100/($_data->win_ko+$_data->win_to))|number_format:1|escape}}%)
                      {{/if}}
                      <br>
                    {{'Lose'|translate:'app'|escape}}:&#32;
                      <span class="negative">{{$_data->lose|number_format|escape}}</span>
                      {{if $_data->lose_ko > 0 || $_data->lose_to > 0}}
                        &#32;({{'KO'|translate:'app'|escape}}: {{($_data->lose_ko*100/($_data->lose_ko+$_data->lose_to))|number_format:1|escape}}%)
                      {{/if}}
                      <br>
                    {{'Win %'|translate:'app'|escape}}:&#32;
                      {{if $_data->win == 0 && $_data->lose == 0}}
                        <span class="na">{{'N/A'|translate:'app'|escape}}</span>
                      {{else}}
                        {{($_data->win*100/($_data->win+$_data->lose))|number_format:1|escape}}%
                      {{/if}}<br>
                    {{'Kills'|translate:'app'|escape}}:&#32;
                      <span class="positive">{{$_data->kill_sum|number_format|escape}}</span>
                      {{if $_data->battles_kd > 0}}
                        &#32;({{'Avg.'|translate:'app'|escape}}: <span class="positive">{{($_data->kill_sum/$_data->battles_kd)|number_format:1|escape}}</span>)
                      {{/if}}
                      <br>
                    {{'Deaths'|translate:'app'|escape}}:&#32;
                      <span class="negative">{{$_data->death_sum|number_format|escape}}</span>
                      {{if $_data->battles_kd > 0}}
                        &#32;({{'Avg.'|translate:'app'|escape}}: <span class="negative">{{($_data->death_sum/$_data->battles_kd)|number_format:1|escape}}</span>)
                      {{/if}}
                      <br>
                    {{'K/D'|translate:'app'|escape}}:&#32;
                      {{if $_data->kill_sum == 0 && $_data->death_sum == 0}}
                        <span class="na">{{'N/A'|translate:'app'|escape}}</span>
                      {{else}}
                        {{if $_data->death_sum == 0}}
                          {{$_ratio = 99.99}}
                          {{$_rate = 1}}
                        {{else}}
                          {{$_ratio = ($_data->kill_sum/$_data->death_sum)}}
                          {{$_rate = ($_data->kill_sum/($_data->kill_sum+$_data->death_sum))}}
                        {{/if}}
                        <span class="auto-tooltip" title="{{'Kill Ratio'|translate:'app'|escape}}">
                          {{$_ratio|number_format:2|escape}}
                        </span> (<span class="auto-tooltip" title="{{'Kill Rate'|translate:'app'|escape}}">
                          {{($_rate*100)|number_format:1|escape}}%
                        </span>)
                      {{/if}}<br>
                    {{if $ruleKey === 'nawabari'}}
                      {{'Inked'|translate:'app'|escape}}:&#32;
                        <span class="auto-tooltip" title="{{$_data->point_sum|number_format|escape}}">
                          {{if $_data->point_sum <= 99999}}
                            {{$_data->point_sum|number_format|escape}}
                          {{elseif $_data->point_sum <= 999999}}
                            {{($_data->point_sum/1000)|number_format:0|escape}}k
                          {{elseif $_data->point_sum <= 999999999}}
                            {{($_data->point_sum/1000000)|number_format:2|escape}}M
                          {{else}}
                            {{($_data->point_sum/1000000000)|number_format:2|escape}}G
                          {{/if}}
                        </span>
                        {{if $_data->battles_pt > 0}}
                          &#32;({{'Avg.'|translate:'app'|escape}}: {{($_data->point_sum/$_data->battles_pt)|number_format:1|escape}})
                        {{/if}}
                        <br>
                      {{'Max Inked'|translate:'app'|escape}}: {{$_data->point_max|number_format|escape}}
                    {{else}}
                      {{'Kills/min'|translate:'app'|escape}}:&#32;
                        {{if $_data->battles_time == 0 || $_data->time_sum == 0 || $_data->battles_kd == 0}}
                          <span class="na">{{'N/A'|translate:'app'|escape}}</span>
                        {{else}}
                          <span class="positive">
                            {{($_data->kill_sum*60/$_data->time_sum)|number_format:1|escape}}
                          </span>
                        {{/if}}<br>
                      {{'Deaths/min'|translate:'app'|escape}}:&#32;
                        {{if $_data->battles_time == 0 || $_data->time_sum == 0 || $_data->battles_kd == 0}}
                          <span class="na">{{'N/A'|translate:'app'|escape}}</span>
                        {{else}}
                          <span class="negative">
                            {{($_data->death_sum*60/$_data->time_sum)|number_format:1|escape}}
                          </span>
                        {{/if}}
                    {{/if}}
                    {{*
                    {{$_data|@var_dump|escape}}
                    *}}
                  </td>
                {{/foreach}}
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{BattleFilterWidget route="show/user-stat-by-map-rule-detail" screen_name=$user->screen_name filter=$filter action="summarize" rule=false map=false result=false}}
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
