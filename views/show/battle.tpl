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

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    {{if $battle->battleImageJudge || $battle->battleImageResult}}
      <div class="row">
        {{if $battle->battleImageJudge}}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
            <img src="{{$battle->battleImageJudge->url|escape}}" style="max-width:100%;height:auto">
          </div>
        {{/if}}
        {{if $battle->battleImageResult}}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
            <img src="{{$battle->battleImageResult->url|escape}}" style="max-width:100%;height:auto">
          </div>
        {{/if}}
      </div>
    {{/if}}

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
        <table class="table table-striped">
          <tbody>
            {{if $battle->lobby}}
              <tr>
                <th>
                  {{'Game Mode'|translate:'app'|escape}}
                </th>
                <td>
                  {{$battle->lobby->name|translate:'app-rule'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->rule}}
              <tr>
                <th>
                  {{'Rule'|translate:'app'|escape}}&#32;
                  <a href="{{url route="show/user-stat-by-rule" screen_name=$user->screen_name}}">
                    <span class="fa fa-pie-chart"></span>
                  </a>
                </th>
                <td>
                  <a href="{{url route="show/user" screen_name=$user->screen_name}}?filter[rule]={{$battle->rule->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a>&#32;
                  {{$battle->rule->name|translate:'app-rule'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->map}}
              <tr>
                <th>
                  {{'Map'|translate:'app'|escape}}&#32;
                  <a href="{{url route="show/user-stat-by-map" screen_name=$user->screen_name}}">
                    <span class="fa fa-pie-chart"></span>
                  </a>
                </th>
                <td>
                  <a href="{{url route="show/user" screen_name=$user->screen_name}}?filter[map]={{$battle->map->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a>&#32;
                  {{$battle->map->name|translate:'app-map'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->weapon}}
              <tr>
                <th>{{'Weapon'|translate:'app'|escape}}</th>
                <td>
                  <a href="{{url route="show/user" screen_name=$user->screen_name}}?filter[weapon]={{$battle->weapon->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a>&#32;
                  {{$battle->weapon->name|translate:'app-weapon'|escape}}
                  &#32;(
                  {{$battle->weapon->subweapon->name|default:'?'|translate:'app-subweapon'|escape}} /&#32;
                  {{$battle->weapon->special->name|default:'?'|translate:'app-special'|escape}}
                  )
                </td>
              </tr>
            {{/if}}
            {{if $battle->rank || $battle->rankAfter}}
              <tr>
                <th>{{'Rank'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->rank}}
                    {{$battle->rank->name|translate:'app-rank'|escape}}
                    {{if $battle->rank_exp !== null}}
                      &#32;{{$battle->rank_exp|escape}}
                    {{/if}}
                  {{else}}
                    ?
                  {{/if}}
                  {{if $battle->rankAfter}}
                    &#32;→ {{$battle->rankAfter->name|translate:'app-rank'|escape}}
                    {{if $battle->rank_exp_after !== null}}
                      &#32;{{$battle->rank_exp_after|escape}}
                    {{/if}}
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->level || $battle->level_after}}
              <tr>
                <th>{{'Level'|translate:'app'|escape}}</th>
                <td>
                  {{$battle->level|default:'?'|escape}}
                  {{if $battle->level_after}}
                    &#32;→ {{$battle->level_after|escape}}
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->gender && $battle->festTitle}}
              <tr>
                <th>{{'Splatfest Title'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->my_team_color_rgb}}
                    <span style="color:#{{$battle->my_team_color_rgb|escape}}">
                      ■
                    </span>&#32;
                  {{/if}}
                  {{$battle->festTitle->getName($battle->gender)|translate:'app':['***','***']|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->is_win !== null}}
              <tr>
                <th>{{'Result'|translate:'app'|escape}}</th>
                <td>
                  {{if $gachi && $gachi->is_knock_out !== null}}
                    {{if $gachi->is_knock_out}}
                      <span class="label label-info">
                        {{'KNOCK OUT'|translate:'app'|escape}}
                      </span>
                    {{else}}
                      <span class="label label-warning">
                        {{'TIME IS UP'|translate:'app'|escape}}
                      </span>
                    {{/if}}
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
            {{/if}}
            {{if $battle->rank_in_team}}
              <tr>
                <th>{{'Rank in Team'|translate:'app'|escape}}</th>
                <td>{{$battle->rank_in_team|escape}}</td>
              </tr>
            {{/if}}
            {{if $battle->kill !== null || $battle->death !== null}}
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
            {{/if}}
            {{if $battle->kill !== null && $battle->death !== null}}
              <tr>
                <th>{{'Kill Ratio'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->kill_ratio === null}}
                    {{'N/A'|translate:'app'|escape}}
                  {{else}}
                    {{$battle->kill_ratio|string_format:'%.2f'|escape}}
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            {{$deathReasons = $battle->getBattleDeathReasons()
                ->with(['reason'])
                ->orderBy('{{battle_death_reason}}.[[count]] DESC')
                ->all()}}
            {{if $deathReasons}}
              <tr>
                <th>{{'Cause of Death'|translate:'app'|escape}}</th>
                <td>
                  <table>
                    <tbody>
                      {{foreach $deathReasons as $deathReason}}
                        <tr>
                          <td>{{$deathReason->reason->translatedName|default:'?'|escape}}</td>
                          <td style="padding:0 10px">:</td>
                          <td>
                            {{$params = ['n' => $deathReason->count, 'nFormatted' => $app->formatter->asDecimal($deathReason->count)]}}
                            {{"{nFormatted} {n, plural, =1{time} other{times}}"|translate:'app':$params|escape}}
                          </td>
                        </tr>
                      {{/foreach}}
                    </tbody>
                  </table>
                </td>
              </tr>
            {{/if}}
            {{if $nawabari}}
              {{if $nawabari->my_point}}
                <tr>
                  <th>{{'Turf Inked + Bonus'|translate:'app'|escape}}</th>
                  <td>{{$nawabari->my_point|escape}} P</td>
                </tr>
              {{/if}}
              {{if $nawabari->my_team_final_point || $nawabari->his_team_final_point}}
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
            {{/if}}
            {{if $gachi}}
              {{if $gachi->my_team_count || $gachi->his_team_count}}
                <tr>
                  <th>{{'My Team Count'|translate:'app'|escape}}</th>
                  <td>{{$gachi->my_team_count|default:'?'|escape}}</td>
                </tr>
                <tr>
                  <th>{{'His Team Count'|translate:'app'|escape}}</th>
                  <td>{{$gachi->his_team_count|default:'?'|escape}}</td>
                </tr>
              {{/if}}
            {{/if}}
            {{if $battle->cash || $battle->cash_after}}
              <tr>
                <th>{{'Cash'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->cash === null}}
                    ?
                  {{else}}
                    {{$battle->cash|number_format|escape}}
                  {{/if}}
                  {{if $battle->cash_after !== null}}
                    &#32;→ {{$battle->cash_after|number_format|escape}}
                  {{/if}}
                </td>
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
            {{if $battle->agent}}
              <tr>
                <th>{{'User Agent'|translate:'app'|escape}}</th>
                <td>
                  {{$link = null}}
                  {{if $battle->agent->name === 'IkaLog'}}
                    {{$link = 'https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md'}}
                  {{/if}}
                
                  {{if $link}}
                    <a href="{{$link|escape}}" target="_blank" rel="nofollow">
                  {{/if}}
                  {{$battle->agent->name|escape}}
                  {{if $link}}
                    </a>
                  {{/if}} / {{$battle->agent->version|escape}}
                </td>
            {{/if}}
          </tbody>
        </table>
        <p>
          {{'Note: You can change time zone. Look at navbar.'|translate:'app'|escape}}
        </p>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        {{include "user-miniinfo.tpl" user=$user}}
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="margin-top:15px">
        {{registerJsFile url="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js" async="async"}}
        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-0704984061430053" data-ad-slot="5800809033" data-ad-format="auto"></ins>
        {{registerJs}}(adsbygoogle = window.adsbygoogle || []).push({});{{/registerJs}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerCss}}{{literal}}
th{width:15em}
@media(max-width:30em){th{width:auto}}
.image-container{margin-bottom:15px}
{{/literal}}{{/registerCss}}
