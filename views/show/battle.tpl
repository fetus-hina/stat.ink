{{strip}}
  {{set layout="main.tpl"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <h1>
      {{$name = '{0}-san'|translate:'app':$user->name}}
      {{$title = "Result of {0}'s Battle"|translate:'app':$name}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        {{if $battle->battleImageJudge}}
          <img src="{{$battle->battleImageJudge->url|escape}}" style="max-width:100%;height:auto">
        {{/if}}
        {{if $battle->battleImageResult}}
          <img src="{{$battle->battleImageResult->url|escape}}" style="max-width:100%;height:auto">
        {{/if}}
      </div>
    </div>

    {{$nawabari = null}}
    {{$gachi = null}}
    {{if $battle->isNawabari}}
      {{$nawabari = $battle->battleNawabari}}
    {{/if}}
    {{if $battle->isGachi}}
      {{$gachi = $battle->battleGachi}}
    {{/if}}

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <table class="table table-striped" style="margin-top:15px">
          <tbody>
            <tr>
              <th>{{'Rule'|translate:'app'|escape}}</th>
              <td>
                {{if $battle->rule}}
                  <a href="{{url route="show/user" screen_name=$user->screen_name}}?filter[rule]={{$battle->rule->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a>&#32;
                {{/if}}
                {{$battle->rule->name|default:'?'|translate:'app-rule'|escape}}
              </td>
            </tr>
            <tr>
              <th>{{'Map'|translate:'app'|escape}}</th>
              <td>
                {{if $battle->map}}
                  <a href="{{url route="show/user" screen_name=$user->screen_name}}?filter[map]={{$battle->map->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a>&#32;
                {{/if}}
                {{$battle->map->name|default:'?'|translate:'app-map'|escape}}
              </td>
            </tr>
            <tr>
              <th>{{'Weapon'|translate:'app'|escape}}</th>
              <td>
                {{if $battle->weapon}}
                  <a href="{{url route="show/user" screen_name=$user->screen_name}}?filter[weapon]={{$battle->weapon->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a>&#32;
                {{/if}}
                {{$battle->weapon->name|default:'?'|translate:'app-weapon'|escape}}
              </td>
            </tr>
            <tr>
              <th>{{'Rank'|translate:'app'|escape}}</th>
              <td>{{$battle->rank->name|default:'?'|translate:'app-rank'|escape}}</td>
            </tr>
            <tr>
              <th>{{'Level'|translate:'app'|escape}}</th>
              <td>{{$battle->level|default:'?'|escape}}</th>
            </tr>
            <tr>
              <th>{{'Result'|translate:'app'|escape}}</th>
              <td>
                {{if $gachi && $gachi->is_knock_out === yes}}
                  <span class="label label-info">
                    {{'KNOCK OUT'|translate:'app'|escape}}
                  </span>
                  &#32;
                {{/if}}
                {{if $battle->is_win === true}}
                  <span class="label label-success">
                    {{'WON'|translate:'app'|escape}}
                  </span>
                {{elseif $battle->is_win === false}}
                  <span class="label label-danger">
                    {{'LOST'|translate:'app'|escape}}
                  </span>
                {{else}}
                  {{'?'|translate:'app'|escape}}
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'Rank in Team'|translate:'app'|escape}}</th>
              <td>{{$battle->rank_in_team|default:'?'|escape}}</td>
            </tr>
            <tr>
              <th>{{'Killed/Dead'|translate:'app'|escape}}</th>
              <td>
                {{if $battle->kill === null}}?{{else}}{{$battle->kill|escape}}{{/if}} / {{if $battle->death === null}}?{{else}}{{$battle->death|escape}}{{/if}}
                {{if $battle->kill !== null && $battle->death !== null}}
                  &#32;
                  {{if $battle->kill > $battle->death}}
                    <span class="label label-success">
                      &gt;
                    </span>
                  {{elseif $battle->kill < $battle->death}}
                    <span class="label label-danger">
                      &lt;
                    </span>
                  {{else}}
                    <span class="label label-default">
                      =
                    </span>
                  {{/if}}
                {{/if}}
              </td>
            </tr>
            {{if $nawabari}}
              <tr>
                <th>{{'Turf Inked + Bonus'|translate:'app'|escape}}</th>
                <td>{{$nawabari->my_point|default:'?'|escape}} P</td>
              </tr>
              <tr>
                <th>{{'My Team Score'|translate:'app'|escape}}</th>
                <td>
                  {{$nawabari->my_team_final_point|default:'?'|escape}} P (
                  {{if $nawabari->my_team_final_percent === null}}
                    ?
                  {{else}}
                    {{$nawabari->my_team_final_percent|string_format:'%.1f'|escape}}
                  {{/if}}
                  %)
                </td>
              </tr>
              <tr>
                <th>{{'His Team Score'|translate:'app'|escape}}</th>
                <td>
                  {{$nawabari->his_team_final_point|default:'?'|escape}} P (
                  {{if $nawabari->his_team_final_percent === null}}
                    ?
                  {{else}}
                    {{$nawabari->his_team_final_percent|string_format:'%.1f'|escape}}
                  {{/if}}
                  %)
                </td>
              </tr>
            {{/if}}
            {{if $gachi}}
              <tr>
                <th>{{'My Team Count'|translate:'app'|escape}}</th>
                <td>{{$gachi->my_team_count|default:'?'|escape}}</td>
              </tr>
              <tr>
                <th>{{'His Team Count'|translate:'app'|escape}}</th>
                <td>{{$gachi->my_team_count|default:'?'|escape}}</td>
              </tr>
            {{/if}}
            <tr>
              <th>{{'Battle Start'|translate:'app'|escape}}</th>
              <td>{{$battle->start_at|date_format:'%F %T %Z'|escape}}</td>
            </tr>
            <tr>
              <th>{{'Battle End'|translate:'app'|escape}}</th>
              <td>{{$battle->end_at|date_format:'%F %T %Z'|escape}}</td>
            </tr>
            <tr>
              <th>{{'Data Sent'|translate:'app'|escape}}</th>
              <td>{{$battle->at|date_format:'%F %T %Z'|escape}}</td>
            </tr>
          </tbody>
        </table>
        <p>
          {{'Note: You can change time zone. Look at navbar.'|translate:'app'|escape}}
        </p>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="padding:15px">
        <div style="border:1px solid #ccc;border-radius:5px;padding:15px">
          <h2 style="margin-top:0;margin-bottom:10px">
            <a href="{{url route="show/user" screen_name=$user->screen_name}}">
              {{'{0}-san'|translate:'app':$user->name|escape}}
            </a>
          </h2>
          <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              {{$stat = $user->simpleStatics}}
              <div class="user-label">
                {{'Battles'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                <a href="{{url route="show/user" screen_name=$user->screen_name}}">
                  {{$stat->totalBattleCount|number_format|escape}}
                </a>
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <div class="user-label">
                {{'WP'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                {{if $stat->totalWinRate === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$stat->totalWinRate|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <div class="user-label">
                {{'24H WP'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                {{if $stat->oneDayWinRate === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$stat->oneDayWinRate|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
{{registerCss}}{{literal}}
th{width:15em}
@media(max-width:30em){th{width:auto}}
.user-label{color:#aaa}
.user-number{font-size:1.5em}
{{/literal}}{{/registerCss}}
