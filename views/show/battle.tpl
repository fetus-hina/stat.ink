{{strip}}
  {{set layout="main.tpl"}}
  {{use class="yii\helpers\Url"}}
  {{$user = $battle->user}}
  {{$canonicalUrl = Url::to(['show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id], true)}}
  {{$name = '{0}-san'|translate:'app':$user->name}}
  {{$title = "Result of {0}'s Battle"|translate:'app':$name}}
  {{set title="{{$app->name}} | {{$title}}"}}
  {{$this->registerLinkTag(['rel' => 'canonical', 'href' => $canonicalUrl])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'photo'])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:url', 'content' => $canonicalUrl])|@void}}
  {{$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink'])|@void}}
  {{if $user->twitter != ''}}
    {{$this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@'|cat:$user->twitter])|@void}}
  {{/if}}
  {{$summary = ''}}
  {{if $battle->rule}}
    {{$tmp = $battle->rule->name|translate:'app-rule'}}
    {{$summary = $summary|cat:$tmp:' | '}}
  {{/if}}
  {{if $battle->map}}
    {{$tmp = $battle->map->name|translate:'app-map'}}
    {{$summary = $summary|cat:$tmp:' | '}}
  {{/if}}
  {{if $battle->is_win !== null}}
    {{if $battle->is_win}}
      {{$tmp = 'WON'|translate:'app'}}
    {{else}}
      {{$tmp = 'LOST'|translate:'app'}}
    {{/if}}
    {{$summary = $summary|cat:$tmp:' | '}}
  {{/if}}
  {{$summary = $summary|rtrim:'| '}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $summary])|@void}}

  {{if $battle->previousBattle}}
    {{$_url = Url::to(['show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->previousBattle->id], true)}}
    {{$this->registerLinkTag(['rel' => 'prev', 'href' => $_url])|@void}}
  {{/if}}
  {{if $battle->nextBattle}}
    {{$_url = Url::to(['show/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->nextBattle->id], true)}}
    {{$this->registerLinkTag(['rel' => 'next', 'href' => $_url])|@void}}
  {{/if}}

  <div class="container">
    <h1>
      {{$_url = Url::to(['show/user', 'screen_name' => $user->screen_name])}}
      {{$name = $name|escape}}
      {{$name = '<a href="%s">%s</a>'|sprintf:$_url:$name}}
      {{"Result of {0}'s Battle"|translate:'app':$name}}
    </h1>

    {{if $battle->agent && $battle->agent->isIkaLog}}
      {{if $battle->agent->getIsOldIkalogAsAtTheTime($battle->at)}}
        {{registerCss}}
          .old-ikalog {
            font-weight: bold;
            color: #f00;
          }
        {{/registerCss}}
        <p class="old-ikalog">
          {{'This battle was recorded with outdated IkaLog. Please upgrade to latest version.'|translate:'app'|escape}}
        </p>
      {{/if}}
    {{/if}}

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
          {{$this->registerMetaTag(['name' => 'twitter:image', 'content' => $battle->battleImageResult->url])|@void}}
        {{/if}}
      </div>
    {{/if}}

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        {{if $battle->previousBattle || $battle->nextBattle}}
          <div class="row" style="margin-bottom:15px">
            {{if $battle->previousBattle}}
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <a href="{{url route="show/battle" screen_name=$user->screen_name battle=$battle->previousBattle->id}}" class="btn btn-default">
                  <span class="fa fa-angle-double-left"></span> {{'Prev Battle'|translate:'app'|escape}}
                </a>
              </div>
            {{/if}}
            {{if $battle->nextBattle}}
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right text-right">
                <a href="{{url route="show/battle" screen_name=$user->screen_name battle=$battle->nextBattle->id}}" class="btn btn-default">
                  {{'Next Battle'|translate:'app'|escape}} <span class="fa fa-angle-double-right"></span>
                </a>
              </div>
            {{/if}}
          </div>
        {{/if}}

        <table class="table table-striped" id="battle">
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
            {{if $battle->gender && ($battle->festTitle || $battle->festTitleAfter)}}
              <tr>
                <th>{{'Splatfest Title'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->my_team_color_rgb}}
                    <span style="color:#{{$battle->my_team_color_rgb|escape}}">
                      ■
                    </span>&#32;
                  {{/if}}
                  {{if $battle->festTitle}}
                    {{$battle->festTitle->getName($battle->gender)|translate:'app':['***','***']|escape}}
                    {{if $battle->fest_exp !== null}}
                      &#32;{{$battle->fest_exp|escape}}
                    {{/if}}
                  {{else}}
                    ?
                  {{/if}}
                  {{if $battle->festTitleAfter}}
                    &#32;→ {{$battle->festTitleAfter->getName($battle->gender)|translate:'app':['***','***']|escape}}
                    {{if $battle->fest_exp_after !== null}}
                      &#32;{{$battle->fest_exp_after|escape}}
                    {{/if}}
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->is_win !== null}}
              <tr>
                <th>{{'Result'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->isGachi && $battle->is_knock_out !== null}}
                    {{if $battle->is_knock_out}}
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
            {{if $battle->my_point}}
              <tr>
                <th>{{'Turf Inked + Bonus'|translate:'app'|escape}}</th>
                <td>{{$battle->my_point|escape}} P</td>
              </tr>
            {{/if}}
            {{if $battle->my_team_final_point || $battle->his_team_final_point}}
              <tr>
                <th>{{'My Team Score'|translate:'app'|escape}}</th>
                <td>
                  {{$battle->my_team_final_point|default:'?'|escape}} P (
                  {{if $battle->my_team_final_percent === null}}
                    ?
                  {{else}}
                    {{$battle->my_team_final_percent|string_format:'%.1f'|escape}}
                  {{/if}}
                  %)
                </td>
              </tr>
              <tr>
                <th>{{'His Team Score'|translate:'app'|escape}}</th>
                <td>
                  {{$battle->his_team_final_point|default:'?'|escape}} P (
                  {{if $battle->his_team_final_percent === null}}
                    ?
                  {{else}}
                    {{$battle->his_team_final_percent|string_format:'%.1f'|escape}}
                  {{/if}}
                  %)
                </td>
              </tr>
            {{/if}}
            {{if $battle->my_team_count || $battle->his_team_count}}
              <tr>
                <th>{{'My Team Count'|translate:'app'|escape}}</th>
                <td>{{$battle->my_team_count|default:'?'|escape}}</td>
              </tr>
              <tr>
                <th>{{'His Team Count'|translate:'app'|escape}}</th>
                <td>{{$battle->his_team_count|default:'?'|escape}}</td>
              </tr>
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
        {{if $battle->myTeamPlayers && $battle->hisTeamPlayers}}
          {{if $battle->my_team_color_rgb && $battle->his_team_color_rgb}}
            {{registerCss}}
              #players .bg-my {
                background: #{{$battle->my_team_color_rgb|escape}};
                color: #fff;
                text-shadow: 1px 1px 0 rgba(0,0,0,.8);
              }

              #players .bg-his {
                background: #{{$battle->his_team_color_rgb|escape}};
                color: #fff;
                text-shadow: 1px 1px 0 rgba(0,0,0,.8);
              }
            {{/registerCss}}
          {{/if}}
          {{registerCss}}
            #players .its-me {
              background: #ffffcc;
            }
          {{/registerCss}}
          {{$hideRank = true}}
          {{$hidePoint = true}}
          {{if !$battle->rule || $battle->rule->key !== 'nawabari'}}
            {{$hideRank = false}}
          {{/if}}
          {{if !$battle->rule || ($battle->rule->key === 'nawabari' && (!$battle->lobby || $battle->lobby->key !== 'fest'))}}
            {{$hidePoint = false}}
          {{/if}}
          <table class="table table-bordered" id="players">
            <thead>
              <tr>
                <th style="width:1em"></th>
                <th class="col-weapon">{{'Weapon'|translate:'app'|escape}}</th>
                <th class="col-level">{{'Level'|translate:'app'|escape}}</th>
                {{if !$hideRank}}
                  <th class="col-rank">{{'Rank'|translate:'app'|escape}}</th>
                {{/if}}
                {{if !$hidePoint}}
                  <th class="col-point">{{'Point'|translate:'app'|escape}}</th>
                {{/if}}
                <th class="col-kd">{{'k'|translate:'app'|escape}}/{{'d'|translate:'app'|escape}}</th>
                <th class="col-kr">{{'KR'|translate:'app'|escape}}</th>
            </thead>
            <tbody>
              {{if $battle->is_win === false}}
                {{$teams = ['his', 'my']}}
              {{else}}
                {{$teams = ['my', 'his']}}
              {{/if}}
              {{foreach $teams as $teamKey}}
                {{$attr = $teamKey|cat:'TeamPlayers'}}
                {{$totalKill = 0}}
                {{$totalDeath = 0}}
                {{$totalPoint = 0}}
                {{$hasNull = false}}
                {{foreach $battle->$attr as $player}}
                  {{if $player->kill === null || $player->death === null}}
                    {{$hasNull = true}}
                  {{else}}
                    {{$totalKill = $totalKill + $player->kill}}
                    {{$totalDeath = $totalDeath + $player->death}}
                  {{/if}}
                  {{if $totalPoint !== null && $player->point !== null}}
                    {{$totalPoint = $totalPoint + $player->point}}
                  {{else}}
                    {{$totalPoint = null}}
                  {{/if}}
                {{/foreach}}
                <tr class="bg-{{$teamKey|escape}}">
                  <th colspan="2">
                    {{if $teamKey === 'my'}}
                      {{'Good Guys'|translate:'app'|escape}}
                    {{else}}
                      {{'Bad Guys'|translate:'app'|escape}}
                    {{/if}}
                  </th>
                  <td></td>
                  {{if !$hideRank}}
                    <td></td>
                  {{/if}}
                  {{if !$hidePoint}}
                    <td class="text-right">
                      {{if $totalPoint !== null}}
                        {{$totalPoint|number_format|escape}}
                      {{/if}}
                    </td>
                  {{/if}}
                  <td class="text-center">
                    {{if !$hasNull}}
                      {{$totalKill|escape}} / {{$totalDeath|escape}}
                    {{/if}}
                  </td>
                  <td class="text-right">
                    {{if !$hasNull}}
                      {{if $totalDeath == 0}}
                        {{if $totalKill != 0}}
                          99.99
                        {{/if}}
                      {{else}}
                        {{($totalKill/$totalDeath)|string_format:'%.2f'|escape}}
                      {{/if}}
                    {{/if}}
                  </td>
                </tr>
                {{foreach $battle->$attr as $player}}
                  <tr class="{{if $player->is_me}}its-me{{/if}}">
                    <td class="bg-{{$teamKey|escape}}"></td>
                    <td class="col-weapon">
                      {{if $player->weapon}}
                        <span title="{{*
                            *}}{{'Sub:'|translate:'app'|escape}}{{$player->weapon->subweapon->name|default:'?'|translate:'app-subweapon'|escape}} / {{*
                            *}}{{'Special:'|translate:'app'|escape}}{{$player->weapon->special->name|default:'?'|translate:'app-special'|escape}}" class="auto-tooltip">
                          {{$player->weapon->name|default:''|translate:'app-weapon'|escape}}
                        </span>
                      {{/if}}
                    </td>
                    <td class="col-level text-right">
                      {{$player->level|escape}}
                    </td>
                    {{if !$hideRank}}
                      <td class="col-rank text-center">
                        {{$player->rank->name|default:''|translate:'app-rank'|escape}}
                      </td>
                    {{/if}}
                    {{if !$hidePoint}}
                      <td class="col-point text-right">
                        {{$player->point|number_format|escape}}
                      </td>
                    {{/if}}
                    <td class="col-kd text-center">
                      {{if $player->kill === null}}
                        ?
                      {{else}}
                        {{$player->kill|escape}}
                      {{/if}} / {{if $player->death === null}}
                        ?
                      {{else}}
                        {{$player->death|escape}}
                      {{/if}} {{if $player->kill !== null && $player->death !== null}}
                        {{if $player->kill > $player->death}}
                          <span class="label label-success">&gt;</span>
                        {{elseif $player->kill < $player->death}}
                          <span class="label label-danger">&lt;</span>
                        {{else}}
                          <span class="label label-default">=</span>
                        {{/if}}
                      {{/if}}
                    </td>
                    <td class="col-kr text-right">
                      {{if $player->kill !== null && $player->death !== null}}
                        {{if $player->death === 0}}
                          {{if $player->kill !== 0}}
                            99.99
                          {{/if}}
                        {{else}}
                          {{($player->kill/$player->death)|string_format:'%.2f'|escape}}
                        {{/if}}
                      {{/if}}
                    </td>
                  </tr>
                {{/foreach}}
              {{/foreach}}
            </tbody>
          </table>
        {{/if}}
        {{if $battle->events}}
          {{$events = $battle->events|@json_decode:false}}
          {{if $events}}
            <script>
              window.battleEvents = {{$events|json_encode}}
            </script>
            <div class="graph" id="timeline">
            </div>
            {{\app\assets\FlotAsset::register($this)|@void}}
            {{\app\assets\FlotIconAsset::register($this)|@void}}
            {{\app\assets\GraphIconAsset::register($this)|@void}}
            {{$iconAsset = $app->assetManager->getBundle('app\assets\GraphIconAsset')}}
            {{registerCss}}
              .graph{height:300px}
            {{/registerCss}}
            {{registerJs position="POS_BEGIN"}}
              window.graphIcon = {
                dead: (function(){
                  var i = new Image;
                  i.src = "{{$app->assetmanager->getAssetUrl($iconAsset, 'dead.png')|escape:javascript}}";
                  return i;
                })(),
                killed: (function(){
                  var i = new Image;
                  i.src = "{{$app->assetmanager->getAssetUrl($iconAsset, 'killed.png')|escape:javascript}}";
                  return i;
                })()
              };
            {{/registerJs}}
            {{registerJs}}
              (function($) {
                {{if $battle->rule}}
                  var isNawabari = {{if $battle->rule->key === 'nawabari'}}true{{else}}false{{/if}};
                  var isGachi = !isNawabari;
                  var ruleKey = '{{$battle->rule->key|escape:javascript}}';
                {{else}}
                  var isNawabari = false;
                  var isGachi = false;
                  var ruleKey = null;
                {{/if}}

                var $graphs = $('.graph');
                window.battleEvents.sort(function(a,b){
                  return a.at - b.at;
                });
              
                function drawTimelineGraph() {
                  var $graph_ = $graphs.filter('#timeline');
                  var inkedData = isNawabari
                    ? window.battleEvents.filter(function(v){
                        return (v.type === "score" && v.score) || (v.type === "point" && v.point);
                      }).map(function(v){
                        return [
                          v.at,
                          v.type === "score" ? v.score : v.point
                        ];
                      })
                    : [];

                  {{* ガチエリアのカウントデータ *}}
                  {{* ペナルティデータは未実装 *}}
                  var myAreaData = [];
                  var hisAreaData = [];
                  if (isGachi && ruleKey === 'area') {
                    $.each(
                      window.battleEvents.filter(function(v){
                        return v.type === "splatzone";
                      }),
                      function(){
                        myAreaData.push([this.at, 100 - this.my_team_count]);
                        hisAreaData.push([this.at, 100 - this.his_team_count]);
                      }
                    );
                    if (myAreaData.length > 0 || hisAreaData.length > 0) {
                      myAreaData.unshift([0, 0]);
                      hisAreaData.unshift([0, 0]);
                    }
                  }

                  var objectiveData = (isGachi && ruleKey !== 'area')
                    ? window.battleEvents.filter(function(v){
                        return v.type === "objective";
                      }).map(function(v){
                        return [
                          v.at,
                          v.position
                        ];
                      })
                    : [];

                  {{* window.battleEvents からヤグラ・ホコ時の対象位置→ポイント変換 *}}
                  {{* isPositive: 自分のチームを対象にするとき true, 相手チームの時 false *}}
                  var createObjectPositionPoint = function (isPositive) {
                    var coeffient = isPositive ? 1 : -1;

                    {{* 対象チームを正とした、objective イベントだけのリストを作成 *}}
                    var list = window.battleEvents.filter(function (v) {
                      return v.type === "objective";
                    }).map(function(v) {
                      var o = $.extend(true, {}, v);
                      o.position = o.position * coeffient;
                      return o;
                    });

                    {{* ポイント更新したタイミングのリストを作成 *}}
                    var max = 0;
                    var lastEventAt = null; {{* ret に積まれている最後の時間と等しい場合は null *}}
                    var ret = [[0, 0]];
                    $.each(list, function () {
                      var v = this;
                      if (v.position > max) {
                        {{* 勾配を正しく描画するために直前のイベントの時間とスコアを与える *}}
                        if (lastEventAt !== null) {
                          ret.push([v.at, coeffient * max]);
                        }

                        {{* スコア更新おめ *}}
                        max = v.position;
                        ret.push([v.at, coeffient * v.position]);
                        lastEventAt = null;
                      } else {
                        lastEventAt = v.at;
                      }
                    });

                    {{* 最後まで描画するために最後のイベントの時間のデータを作る *}}
                    lastEventAt = Math.max.apply(null, window.battleEvents.map(function (v) {
                      return v.at;
                    }));
                    ret.push([lastEventAt, coeffient * max]);

                    return ret;
                  };

                  var hsv2rgb = function (h, s, v) {
                    var r, g, b;
                    while (h < 0) {
                      h += 360;
                    }
                    h = (~~h) % 360;
                    v = v * 255;
                    if (s == 0) {
                      r = g = b = v;
                    } else {
                      var i = Math.floor(h / 60) % 6,
                          f = (h / 60) - i,
                          p = v * (1 - s),
                          q = v * (1 - f * s),
                          t = v * (1 - (1 - f) * s);

                      switch (i) {
                        case 0:
                          r = v;
                          g = t;
                          b = p;
                          break;

                        case 1:
                          r = q;
                          g = v;
                          b = p;
                          break;

                        case 2:
                          r = p;
                          g = v;
                          b = t;
                          break;

                        case 3:
                          r = p;
                          g = q;
                          b = v;
                          break;

                        case 4:
                          r = t;
                          g = p;
                          b = v;
                          break;

                        case 5:
                          r = v;
                          g = p;
                          b = q;
                          break;
                      }
                    }
                    return [
                      Math.round(r),
                      Math.round(g),
                      Math.round(b)
                    ];
                  };

                  var pointColorFromHue = function (h) {
                    var rgb = hsv2rgb(h, 0.48, 0.97);
                    var alpha = 0.7;
                    return 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + alpha + ')';
                  };

                  var objectPositionColorFromHues = function (team1, team2) {
                    var hue = Math.round((team1 + team2) / 2); + 180;
                    while (hue < 0) {
                      hue += 360;
                    }
                    hue = hue % 360;
                    var rgb = hsv2rgb(hue, 0.8, 0.7);
                    return 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')';
                  };

                  var iconData = window.battleEvents.filter(function(v){
                    return v.type === "dead" || v.type === "killed";
                  }).map(function(v){
                    var size = Math.max(18, Math.ceil($graph_.height() * 0.075));
                    return [
                      window.graphIcon[v.type].src, v.at, size, size
                    ];
                  });

                  var markings = window.battleEvents.filter(function(v){
                    return v.type === "dead";
                  }).map(function(v){
                    return {
                      xaxis: {
                        from: v.at,
                        to: v.at + 8.5
                      },
                      color: "rgba(255,200,200,0.6)"
                    };
                  });

                  if (inkedData.length > 0) {
                    inkedData.unshift([0, null]);
                    (function () {
                      var lastEventAt = Math.max.apply(null, window.battleEvents.map(function (v) {
                        return v.at;
                      }));
                      var lastScore = inkedData.slice(-1)[0][1];
                      inkedData.push([lastEventAt, lastScore]);
                    })();
                  }
                  if (objectiveData.length > 0) {
                    objectiveData.unshift([0, 0]);
                  }

                  $graph_.each(function () {
                    var $graph = $(this);
                    if (inkedData.length < 1 && iconData.length < 1) {
                      $graph.hide();
                    }
                    var data = [];
                    if (inkedData.length > 0) {
                      data.push({
                        label: "{{'Turf Inked'|translate:'app'|escape:'javascript'}}",
                        data: inkedData,
                        color: window.colorScheme.graph1
                      });
                    }
                    if (myAreaData.length > 0 || hisAreaData.length > 0) {
                      console.log(myAreaData, hisAreaData);
                      data.push({
                        label: "{{'Count (Good Guys)'|translate:'app'|escape:'javascript'}}",
                        data: myAreaData,
                        color: {{if $battle->my_team_color_hue !== null}}pointColorFromHue({{$battle->my_team_color_hue|intval}}){{else}}null{{/if}},
                        lines: {
                          show: true,
                          fill: true,
                        },
                        shadowSize: 0
                      });
                      data.push({
                        label: "{{'Count (Bad Guys)'|translate:'app'|escape:'javascript'}}",
                        data: hisAreaData,
                        color: {{if $battle->his_team_color_hue !== null}}pointColorFromHue({{$battle->his_team_color_hue|intval}}){{else}}null{{/if}},
                        lines: {
                          show: true,
                          fill: true,
                        },
                        shadowSize: 0
                      });
                    }
                    if (objectiveData.length > 0) {
                      data.push({
                        label: "{{'Count (Good Guys)'|translate:'app'|escape:'javascript'}}",
                        data: createObjectPositionPoint(true),
                        color: {{if $battle->my_team_color_hue !== null}}pointColorFromHue({{$battle->my_team_color_hue|intval}}){{else}}null{{/if}},
                        lines: {
                          show: true,
                          fill: true,
                          lineWidth: 1
                        },
                        shadowSize: 0
                      });
                      data.push({
                        label: "{{'Count (Bad Guys)'|translate:'app'|escape:'javascript'}}",
                        data: createObjectPositionPoint(false),
                        color: {{if $battle->his_team_color_hue !== null}}pointColorFromHue({{$battle->his_team_color_hue|intval}}){{else}}null{{/if}},
                        lines: {
                          show: true,
                          fill: true,
                          lineWidth: 1
                        },
                        shadowSize: 0
                      });
                      data.push({
                        label: "{{'Position'|translate:'app'|escape:'javascript'}}",
                        data: objectiveData,
                        color: {{if $battle->my_team_color_hue && $battle->his_team_color_hue !== null}}
                          objectPositionColorFromHues({{$battle->my_team_color_hue|intval}}, {{$battle->his_team_color_hue|intval}})
                        {{else}}
                          '#edc240'
                        {{/if}},
                        lines: {
                          show: true,
                          fill: false
                        },
                        shadowSize: 0
                      });
                    }
                    data.push({
                      data: iconData,
                      icons: {
                        show: true,
                        tooltip: function (x, $this) {
                          var t = Math.floor(x);
                          var m = Math.floor(t / 60);
                          var s = t % 60;
                          var value = m + ':' + (s < 10 ? '0' + s : s);
                          $this.attr('title', value)
                            .tooltip({'container': 'body'});
                        },
                      }
                    });
              
                    $.plot($graph, data, {
                      xaxis: {
                        min: 0,
                        minTickSize: 30,
                        tickFormatter: function (v) {
                          v = Math.floor(v);
                          var m = Math.floor(v / 60);
                          var s = Math.floor(v % 60);
                          return m + ":" + (s < 10 ? "0" + s : s);
                        }
                      },
                      yaxis: {
                        minTickSize: isNawabari ? 100 : 10,
                        min: isNawabari || ruleKey === 'area' ? 0 : -100,
                        max: isNawabari ? null : 100
                      },
                      legend: {
                        position: 'nw'
                      },
                      series: {
                        lines: {
                          show: true, fill: true
                        }
                      },
                      grid: {
                        markings: markings
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
                    drawTimelineGraph();
                  }, 33);
                }).resize();
              })(jQuery);
            {{/registerJs}}
          {{/if}}
        {{/if}}
        {{if !$app->user->isGuest && $app->user->identity->id == $user->id}}
          <p class="right">
            <a href="{{url route="show/edit-battle" screen_name=$user->screen_name battle=$battle->id}}" class="btn btn-default">
              {{'Edit'|translate:'app'|escape}}
            </a>
          </p>
        {{/if}}
        <p>
          {{'Note: You can change time zone. Look at navbar.'|translate:'app'|escape}}
        </p>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" style="margin-top:15px">
        {{include file="@app/views/includes/ad.tpl"}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerCss}}{{literal}}
#battle th{width:15em}
@media(max-width:30em){#battle th{width:auto}}
.image-container{margin-bottom:15px}
{{/literal}}{{/registerCss}}
