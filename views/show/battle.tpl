{{strip}}
  {{\app\assets\GearCalcAsset::register($this)|@void}}
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
  {{registerJs position="POS_BEGIN"}}
    window.gearAbilities = {{$battle->gearAbilities|json_encode}};
  {{/registerJs}}

  {{use class="app\models\Special"}}
  {{$specials = Special::find()->asArray()->all()}}

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
          {{'This battle was recorded with an outdated version of IkaLog. Please upgrade to the latest version.'|translate:'app'|escape}}
        </p>
      {{/if}}
    {{/if}}

    {{SnsWidget}}

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
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        {{if $battle->previousBattle || $battle->nextBattle}}
          <div class="row" style="margin-bottom:15px">
            {{if $battle->previousBattle}}
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <a href="{{url route="show/battle" screen_name=$user->screen_name battle=$battle->previousBattle->id}}" class="btn btn-default">
                  <span class="fa fa-angle-double-left left"></span>{{'Prev. Battle'|translate:'app'|escape}}
                </a>
              </div>
            {{/if}}
            {{if $battle->nextBattle}}
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right text-right">
                <a href="{{url route="show/battle" screen_name=$user->screen_name battle=$battle->nextBattle->id}}" class="btn btn-default">
                  {{'Next Battle'|translate:'app'|escape}}<span class="fa fa-angle-double-right right"></span>
                </a>
              </div>
            {{/if}}
          </div>
        {{/if}}

        {{if $battle->link_url}}
          {{use class="app\components\widgets\EmbedVideo"}}
          {{if EmbedVideo::isSupported($battle->link_url)}}
            {{EmbedVideo::widget(['url' => $battle->link_url])}}
            {{registerCss}}.video{margin-bottom:15px}{{/registerCss}}
          {{/if}}
        {{/if}}

        <table class="table table-striped" id="battle">
          <tbody>
            {{if $battle->lobby}}
              <tr>
                <th>
                  {{'Lobby'|translate:'app'|escape}}
                </th>
                <td>
                  {{$battle->lobby->name|translate:'app-rule'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->rule}}
              <tr>
                <th>
                  {{'Mode'|translate:'app'|escape}}&#32;
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
                  {{'Stage'|translate:'app'|escape}}&#32;
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
            {{if $battle->festTitle || $battle->festTitleAfter}}
              <tr>
                <th>{{'Splatfest Title'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->my_team_color_rgb}}
                    <span style="color:#{{$battle->my_team_color_rgb|escape}}">
                      ■
                    </span>&#32;
                  {{/if}}
                  {{if $battle->festTitle}}
                    {{$battle->festTitle->getName($battle->gender)|translate:'app-fest':['***','***']|escape}}
                    {{if $battle->fest_exp !== null}}
                      &#32;{{$battle->fest_exp|escape}}
                    {{/if}}
                  {{else}}
                    ?
                  {{/if}}
                  {{if $battle->festTitleAfter}}
                    &#32;→ {{$battle->festTitleAfter->getName($battle->gender)|translate:'app-fest':['***','***']|escape}}
                    {{if $battle->fest_exp_after !== null}}
                      &#32;{{$battle->fest_exp_after|escape}}
                    {{/if}}
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->fest_power}}
              <tr>
                <th>{{'Splatfest Power'|translate:'app'|escape}}</th>
                <td>{{$battle->fest_power|escape}}</td>
              </tr>
            {{/if}}
            {{if $battle->my_team_power || $battle->his_team_power}}
              <tr>
                <th>{{'My Team Splatfest Power'|translate:'app'|escape}}</th>
                <td>{{$battle->my_team_power|default:'?'|escape}}</td>
              </tr>
              <tr>
                <th>{{'Their Team Splatfest Power'|translate:'app'|escape}}</th>
                <td>{{$battle->his_team_power|default:'?'|escape}}</td>
              </tr>
            {{/if}}
            {{if $battle->is_win !== null}}
              <tr>
                <th>{{'Result'|translate:'app'|escape}}</th>
                <td>
                  {{if $battle->isGachi && $battle->is_knock_out !== null}}
                    {{if $battle->is_knock_out}}
                      <span class="label label-info">
                        {{'KNOCKOUT'|translate:'app'|escape}}
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
                    {{$battle->kill_ratio|number_format:2|escape}}
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
                    {{$battle->my_team_final_percent|number_format:1|escape}}
                  {{/if}}
                  %)
                </td>
              </tr>
              <tr>
                <th>{{'Their Team Score'|translate:'app'|escape}}</th>
                <td>
                  {{$battle->his_team_final_point|default:'?'|escape}} P (
                  {{if $battle->his_team_final_percent === null}}
                    ?
                  {{else}}
                    {{$battle->his_team_final_percent|number_format:1|escape}}
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
                <th>{{'Their Team Count'|translate:'app'|escape}}</th>
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
            {{if $battle->headgear || $battle->clothing || $battle->shoes}}
              <tr>
                <th>{{'Gear'|translate:'app'|escape}}</th>
                <td>
                  <table class="table table-bordered table-condensed" style="margin-bottom:0">
                    <thead>
                      <tr>
                        <th></th>
                        <th>{{'Headgear'|translate:'app-gear'|escape}}</th>
                        <th>{{'Clothing'|translate:'app-gear'|escape}}</th>
                        <th>{{'Shoes'|translate:'app-gear'|escape}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      {{if $battle->headgear->gear_id || $battle->clothing->gear_id || $battle->shoes->gear_id}}
                        <tr>
                          <th>{{'Gear'|translate:'app'|escape}}</th>
                          <td>{{$battle->headgear->gear->name|default:'?'|translate:'app-gear'|escape}}</td>
                          <td>{{$battle->clothing->gear->name|default:'?'|translate:'app-gear'|escape}}</td>
                          <td>{{$battle->shoes->gear->name|default:'?'|translate:'app-gear'|escape}}</td>
                        </tr>
                      {{/if}}
                      <tr>
                        <th>{{'Primary Ability'|translate:'app'|escape}}</th>
                        <td>{{$battle->headgear->primaryAbility->name|default:'?'|translate:'app-ability'|escape}}</td>
                        <td>{{$battle->clothing->primaryAbility->name|default:'?'|translate:'app-ability'|escape}}</td>
                        <td>{{$battle->shoes->primaryAbility->name|default:'?'|translate:'app-ability'|escape}}</td>
                      </tr>
                      <tr>
                        <th rowspan="3">{{'Secondary Abilities'|translate:'app'|escape}}</th>
                        <td>{{$battle->headgear->secondaries.0->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}</td>
                        <td>{{$battle->clothing->secondaries.0->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}</td>
                        <td>{{$battle->shoes->secondaries.0->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}</td>
                      </tr>
                      <tr>
                        <td>
                          {{if $battle->headgear->secondaries|@count > 1}}
                            {{$battle->headgear->secondaries.1->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}
                          {{/if}}
                        </td>
                        <td>
                          {{if $battle->clothing->secondaries|@count > 1}}
                            {{$battle->clothing->secondaries.1->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}
                          {{/if}}
                        </td>
                        <td>
                          {{if $battle->shoes->secondaries|@count > 1}}
                            {{$battle->shoes->secondaries.1->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}
                          {{/if}}
                        </td>
                      </tr>
                      <tr>
                        <td>
                          {{if $battle->headgear->secondaries|@count > 2}}
                            {{$battle->headgear->secondaries.2->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}
                          {{/if}}
                        </td>
                        <td>
                          {{if $battle->clothing->secondaries|@count > 2}}
                            {{$battle->clothing->secondaries.2->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}
                          {{/if}}
                        </td>
                        <td>
                          {{if $battle->shoes->secondaries|@count > 2}}
                            {{$battle->shoes->secondaries.2->ability->name|default:'(Locked)'|translate:'app-ability'|escape}}
                          {{/if}}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <p class="text-right">
                    <a href="#effect">
                      {{'Ability Effect'|translate:'app'|escape}}
                    </a>
                  </p>
                </td>
              </tr>
            {{/if}}
            {{if $battle->link_url != ''}}
              <tr>
                <th>{{'Link'|translate:'app'|escape}}</th>
                <td>
                  <a href="{{$battle->link_url|escape}}" rel="nofollow">
                    {{$battle->link_url|decode_idn|escape}}
                  </a>
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
            {{if $battle->ua_variables}}
              <tr>
                <th>{{'Extra Data'|translate:'app'|escape}}</th>
                <td>
                  <table class="table" style="margin-bottom:0">
                    {{foreach $battle->extraData as $k => $v}}
                      <tr>
                        <th>{{$k|translate:'app-ua-vars'|escape}}</th>
                        <td>{{$v|translate:'app-ua-vars-v'|escape}}</td>
                      </tr>
                    {{/foreach}}
                  </table>
                </td>
              </tr>
            {{/if}}
            {{if $battle->note != ''}}
              <tr>
                <th>{{'Note'|translate:'app'|escape}}</th>
                <td>
                  {{$battle->note|escape|nl2br}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->private_note != ''}}
              {{if !$app->user->isGuest && $app->user->identity->id == $user->id}}
                <tr>
                  <th>{{'Note (private)'|translate:'app'|escape}}</th>
                  <td>
                    <button class="btn btn-default" id="private-note-show">
                      <span class="fa fa-lock fa-fw"></span>
                    </button>
                    <div id="private-note">{{$battle->private_note|escape|nl2br}}</div>
                    {{registerCss}}
                      #private-note{display:none}
                    {{/registerCss}}
                    {{registerJs}}
                      (function($){
                        "use strict";
                        var $btn = $('#private-note-show');
                        var $txt = $('#private-note');
                        var $i = $('.fa', $btn);
                        $btn.hover(
                          function() {
                            $i.removeClass('fa-lock').addClass('fa-unlock-alt');
                          },
                          function() {
                            $i.removeClass('fa-unlock-alt').addClass('fa-lock');
                          }
                        ).click(function () {
                          $btn.hide();
                          $txt.show();
                        });
                      })(jQuery);
                    {{/registerJs}}
                  </td>
                </tr>
              {{/if}}
            {{/if}}
            <tr>
              <th>{{'Game Version'|translate:'app'|escape}}</th>
              <td>
                {{if $battle->splatoonVersion}}
                  {{$battle->splatoonVersion->name|escape}}
                {{else}}
                  {{'Unknown'|translate:'app'|escape}}
                {{/if}}
              </td>
            </tr>
          </tbody>
        </table>
        <p>
          {{'Note: You can change the time zone via the navbar.'|translate:'app'|escape}}
        </p>
        {{if !$app->user->isGuest && $app->user->identity->id == $user->id}}
          <p class="text-right">
            <a href="{{url route="show/edit-battle" screen_name=$user->screen_name battle=$battle->id}}" class="btn btn-default">
              {{'Edit'|translate:'app'|escape}}
            </a>
          </p>
        {{/if}}
        {{$hasExtendedData = false}}
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
          {{$hasExtendedData = true}}
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
                  <th class="col-point">{{'Points'|translate:'app'|escape}}</th>
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
                    {{if $teamKey@first}}
                      {{* 勝利チーム側の合計からは勝利ボーナスを消す *}}
                      {{$totalPoint = $totalPoint - 300}}
                    {{/if}}
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
                          {{99.99|number_format:2|escape}}
                        {{/if}}
                      {{else}}
                        {{($totalKill/$totalDeath)|number_format:2|escape}}
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
                        {{if $player->point !== null}}
                          {{$player->point|number_format|escape}}
                        {{/if}}
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
                            {{99.99|number_format:2|escape}}
                          {{/if}}
                        {{else}}
                          {{($player->kill/$player->death)|number_format:2|escape}}
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
            {{$hasExtendedData = true}}
            <script>
              window.battleEvents = {{$events|json_encode}};
              window.deathReasons = {{$battle->getDeathReasonNamesFromEvents()|json_encode}};
            </script>
            <div id="timeline-legend">
            </div>
            <div class="graph" id="timeline">
            </div>
            {{\jp3cki\yii2\flot\FlotAsset::register($this)|@void}}
            {{\app\assets\FlotIconAsset::register($this)|@void}}
            {{\app\assets\GraphIconAsset::register($this)|@void}}
            {{$iconAsset = $app->assetManager->getBundle('app\assets\GraphIconAsset')}}
            {{registerCss}}
              .graph{height:300px}
            {{/registerCss}}
            {{registerJs position="POS_BEGIN"}}
              (function(){
                {{if $battle->my_team_color_hue === null || $battle->his_team_color_hue === null}}
                  {{$myHue = null}}
                  {{$hisHue = null}}
                {{else}}
                  {{$myHue = (round($battle->my_team_color_hue / 2) * 2)}}
                  {{$hisHue = (round($battle->his_team_color_hue / 2) * 2)}}
                {{/if}}
                var imgLoad = function (src) {
                  var img = new Image;
                  img.src = src;
                  return img;
                };
                window.graphIcon = {
                  dead: imgLoad("{{$app->assetmanager->getAssetUrl($iconAsset, 'dead/default.png')|escape:javascript}}"),
                  killed: imgLoad("{{$app->assetmanager->getAssetUrl($iconAsset, 'killed/default.png')|escape:javascript}}"),
                  {{if $myHue !== null && $hisHue !== null}}
                    weGot: imgLoad(
                      {{$tmp = 'gachi/'|cat:$myHue:'.png'}}
                      "{{$app->assetmanager->getAssetUrl($iconAsset, $tmp)|escape:javascript}}"
                    ),
                    theyGot: imgLoad(
                      {{$tmp = 'gachi/'|cat:$hisHue:'.png'}}
                      "{{$app->assetmanager->getAssetUrl($iconAsset, $tmp)|escape:javascript}}"
                    ),
                    weLost: imgLoad(
                      "{{$app->assetmanager->getAssetUrl($iconAsset, 'gachi/default.png')|escape:javascript}}"
                    ),
                    theyLost: imgLoad(
                      "{{$app->assetmanager->getAssetUrl($iconAsset, 'gachi/default.png')|escape:javascript}}"
                    ),
                  {{else}}
                    weGot: null,
                    theyGot: null,
                    weLost: null,
                    theyLost: null,
                  {{/if}}
                  weLead: null,
                  theyLead: null,
                  specials: {
                    {{foreach $specials as $special}}
                      {{$special.key|escape:javascript}}: imgLoad(
                        {{$tmp = 'specials/'|cat:$special.key:'.png'}}
                        "{{$app->assetManager->getAssetUrl($iconAsset, $tmp)|escape:javascript}}"
                      ),
                    {{/foreach}}
                  },
                  specialCharged: imgLoad(
                    "{{$app->assetManager->getAssetUrl($iconAsset, 'special_charged.png')|escape:javascript}}"
                  ),
                };
              })();
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

                  var controlColorFromHue = function (h) {
                    var rgb = hsv2rgb(h, 0.95, 0.50);
                    var alpha = 0.7;
                    return 'rgba(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ',' + alpha + ')';
                  };

                  var inklingColorFromHue = function (h) {
                    var rgb = hsv2rgb(h, 0.95, 0.50);
                    var alpha = 0.5;
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

                  var isIdentifiedWhoseSpecial = (function () {
                    return window.battleEvents.filter(function (v) {
                      return v.at && v.type === "special_weapon" && v.me;
                    }).length > 0;
                  })();

                  var iconData = window.battleEvents.filter(function(v){
                    if (!v.at) {
                      return false;
                    }
                    if (v.type === "killed" || v.type === "dead" || v.type === "special_charged") {
                      return true;
                    }
                    if (v.type === "special_weapon") {
                      return (
                        {{foreach $specials as $special}}
                          {{if !$special@first}}
                            ||
                          {{/if}}
                          (v.special_weapon === "{{$special.key|escape:javascript}}")
                        {{/foreach}}
                      );
                    }
                    return false;
                  }).map(function(v){
                    var size = Math.max(18, Math.ceil($graph_.height() * 0.075));
                    if (v.type === "ranked_battle_event") {
                      return [
                        window.graphIcon[(function(type) {
                          switch (type) {
                            case "we_got": return "weGot";
                            case "we_lost": return "weLost";
                            case "they_got": return "theyGot";
                            case "they_lost": return "theyLost";
                          }
                        })(v.value)].src, v.at, size, size
                      ];
                    } else if (v.type === "dead") {
                      var reason = (v.reason && window.deathReasons[v.reason])
                        ? window.deathReasons[v.reason]
                        : null;
                      return [
                        window.graphIcon[v.type].src, v.at, size, size, reason
                      ];
                    } else if (v.type === "special_weapon") {
                      var names = {
                        {{foreach $specials as $special}}
                          "{{$special.key|escape:javascript}}": "{{$special.name|translate:'app-special'|escape:javascript}}",
                        {{/foreach}}
                      };
                      return [
                        window.graphIcon.specials[v.special_weapon].src,
                        v.at,
                        size,
                        size,
                        names[v.special_weapon],
                        function ($img) {
                          var data = this;
                          if (isIdentifiedWhoseSpecial) {
                            $img.css({
                              opacity: v.me ? 1.0 : 0.4,
                            });
                          }
                        }
                      ];
                    } else if (v.type === "special_charged") {
                      return [
                        window.graphIcon.specialCharged.src, v.at, size, size, "{{'Special Charged'|translate:'app'|escape:javascript}}"
                      ];
                    } else {
                      return [
                        window.graphIcon[v.type].src, v.at, size, size
                      ];
                    }
                  });

                  var markings = window.battleEvents.filter(function(v){
                    return v.type === "dead";
                  }).map(function(v){
                    var mainQR = window.gearAbilities.quick_respawn ? window.gearAbilities.quick_respawn.count.main : 0;
                    var subQR = window.gearAbilities.quick_respawn ? window.gearAbilities.quick_respawn.count.sub : 0;
                    var respawnTime = window.getRespawnTime(v.reason ? v.reason : 'unknown', mainQR, subQR);

                    if (console && console.log) {
                      (function() {
                        var reason = v.reason ? v.reason : 'unknown';
                        var reasonName = window.deathReasons[reason] ? window.deathReasons[reason] : '?';
                        console.log(
                          "Dead event: at " + v.at.toFixed(3) + ", " +
                          "splatted by " + reason + "[" + reasonName + "], " +
                          "respawn in " + respawnTime.toFixed(2) + " sec, " +
                          "quick respawn " + mainQR + " + " + subQR
                        );
                      })();
                    }

                    return {
                      xaxis: {
                        from: v.at,
                        to: v.at + respawnTime
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
                    {{if $battle->my_team_color_hue !== null && $battle->his_team_color_hue !== null}}
                      {{* Ranked Battle Events *}}
                      (function() {
                        var rankedEvents = window.battleEvents.filter(
                          function (v) {
                            if (v.type !== "ranked_battle_event") {
                              return false;
                            }
                            switch (v.value) {
                              case "we_got":
                              case "we_lost":
                              case "they_got":
                              case "they_lost":
                                return true;
                              default:
                                return false;
                            }
                          }
                        );
                        if (rankedEvents.length == 0) {
                          return;
                        }
                        var dt = {
                          'neutral': [[0, 380]],
                          'we': [],
                          'they': []
                        };
                        var prevState = 'neutral';
                        var prevTime = null;
                        $.each(rankedEvents, function() {
                          var cur = this;
                          var curState = (function(v) {
                            switch (v) {
                              case "we_got": return "we";
                              case "they_got": return "they";
                              case "we_lost": case "they_lost": return "neutral";
                            }
                          })(cur.value);
                          if (prevState !== curState) {
                            dt[prevState].push([cur.at, 380]);
                            dt[prevState].push([cur.at + 0.0001, null]);
                          }
                          dt[curState].push([cur.at, 380]);
                          prevState = curState;
                          prevTime = cur.at;
                        });
                        {{* 最後まで描くために最後のイベントの時間までのデータを作る *}}
                        dt[prevState].push([
                          window.battleEvents[window.battleEvents.length - 1].at,
                          380 
                        ]);
                        data.push({
                          label: "{{'No one in control'|translate:'app'|escape:'javascript'}}",
                          data: dt.neutral,
                          color: 'rgba(192,192,192,0.85)',
                          yaxis: 2,
                          lines: {
                            fill: false,
                            lineWidth:7
                          },
                          shadowSize: 0
                        });
                        data.push({
                          label: "{{'Good guys are in control'|translate:'app'|escape:'javascript'}}",
                          data: dt.we,
                          color: controlColorFromHue({{$battle->my_team_color_hue|intval}}),
                          yaxis: 2,
                          lines: {
                            fill: false,
                            lineWidth:7
                          },
                          shadowSize: 0
                        });
                        data.push({
                          label: "{{'Bad guys are in control'|translate:'app'|escape:'javascript'}}",
                          data: dt.they,
                          color: controlColorFromHue({{$battle->his_team_color_hue|intval}}),
                          yaxis: 2,
                          lines: {
                            fill: false,
                            lineWidth:7
                          },
                          shadowSize: 0
                        });
                      })();

                      {{* Inklings Track Events *}}
                      (function() {
                        var events = window.battleEvents.filter(
                          function (v) {
                            return v.type === 'alive_inklings';
                          }
                        );
                        if (events.length > 0) {
                          var members = [[], [], [], [], [], [], [], []];
                          var alives = [false, false, false, false, false, false, false, false];
                          $.each(events, function() {
                            var d = this;
                            for (var i = 0; i < d.my_team.length; ++i) {
                              if (!d.my_team[i] && alives[i]) {
                                members[i].push([d.at - 0.001, 363 - i * 17]);
                                members[i].push([d.at, null]);
                              }
                              if (d.my_team[i]) {
                                members[i].push([d.at, 363 - i * 17]);
                              }
                              alives[i] = d.my_team[i];

                              if (!d.his_team[i] && alives[i]) {
                                members[i + 4].push([d.at - 0.001, 295 - i * 17]);
                                members[i + 4].push([d.at, null]);
                              }
                              if (d.his_team[i]) {
                                members[i + 4].push([d.at, 295 - i * 17]);
                              }
                              alives[i + 4] = d.his_team[i];
                            }
                          });
                          for (var i = 0; i < 8; ++i) {
                            data.push({
                              label: (i % 4 === 0)
                                ? ((i === 0)
                                  ? '{{"Good Guys"|translate:"app"|escape:javascript}}'
                                  : '{{"Bad Guys"|translate:"app"|escape:javascript}}')
                                : null,
                              data: members[i],
                              color: inklingColorFromHue(
                                i < 4
                                  ? {{$battle->my_team_color_hue|intval}}
                                  : {{$battle->his_team_color_hue|intval}}
                              ),
                              yaxis: 2,
                              lines: {
                                fill: false,
                                lineWidth: 3
                              },
                              shadowSize: 0
                            });
                          }
                        }
                      })();
                    {{/if}}

                    {{* Special % *}}
                    (function() {
                      var events = window.battleEvents.filter(
                        function (v) {
                          return v.at !== undefined &&
                            v.type === 'special%' &&
                            v.point !== undefined;
                        }
                      );
                      if (events.length > 0) {
                        data.push({
                          label: "{{'Special %'|translate:'app'|escape:javascript}}",
                          data: (function(){
                            var tmp = events.map(function(a){return[a.at,a.point]});
                            tmp.push([0, 0]);
                            tmp.sort(function(a,b){return a[0]-b[0]});
                            return tmp;
                          })(),
                          color: '#888',
                          yaxis: 2,
                          lines: {
                            fill: false,
                            lineWidth: 1,
                          },
                          shadowSize: 1 
                        });
                      }
                    })();

                    data.push({
                      data: iconData,
                      icons: {
                        show: true,
                        tooltip: function (x, $this, userData) {
                          var t = Math.floor(x);
                          var m = Math.floor(t / 60);
                          var s = t % 60;
                          var value = m + ':' + (s < 10 ? '0' + s : s);
                          if (typeof userData === 'string') {
                            value += ' / ' + userData;
                          }
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
                        max: isNawabari ? null : 100,
                        show: true,
                      },
                      y2axis: {
                        min: 0,
                        max: 400,
                        show: false
                      },
                      legend: {
                        container: $('#timeline-legend')
                      },
                      series: {
                        lines: {
                          show: true,
                          fill: true
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

        {{$effects = $battle->abilityEffects}}
        {{if $effects}}
          {{$hasExtendedData = true}}
          <h2 id="effect">
            {{'Ability Effect'|translate:'app'|escape}}
          </h2>
          <table class="table table-striped">
            <tbody>
              {{$_tmp = $effects->attackPct}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Damage'|translate:'app-gearstat'|escape}}</th>
                  <td>
                    {{($_tmp * 100)|number_format:1|escape}} %

                    {{$_attack = $battle->weaponAttack}}
                    {{if $_attack}}
                      &#32;
                      {{$_damage = $_attack->damage * $_tmp}}
                      [{{$_attack->damage|number_format:1|escape}} &times; {{($_tmp * 100)|number_format:1|escape}}% = <strong>{{$_damage|number_format:1|escape}}</strong>]

                      {{$_baseHit2Kill = $_attack->getHitToKill()}}
                      <table class="table table-bordered table-condensed hidden-xs">
                        <thead>
                          <tr>
                            <th colspan="2" rowspan="2">
                              {{'Defense Up'|translate:'app-ability'|escape}}
                            </th>
                            <th colspan="10">{{'Secondary Abilities'|translate:'app'|escape}}</th>
                          </tr>
                          <tr>
                            <th>0</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                          </tr>
                        </thead>
                        <tbody>
                          {{for $_defMain=0 to 3}}
                            <tr>
                            {{if $_defMain === 0}}
                              <th scope="row" rowspan="4">{{'Primary Ability'|translate:'app'|escape}}</th>
                            {{/if}}
                            <th scope="row">{{$_defMain|escape}}</th>
                            {{for $_defSub=0 to 9}}
                              {{$_damage = $effects->calcDamage($_attack->damage, $_defMain, $_defSub)}}
                              {{$_hit2kill = ceil(100 / $_damage)}}
                              <td class="{{if $_hit2kill > $_baseHit2Kill}}danger {{/if}}">
                                {{$_damage|number_format:1|escape}}
                              </td>
                            {{/for}}
                            </tr>
                          {{/for}}
                        </tbody>
                      </table>
                      <table class="table table-bordered table-condensed visible-xs-block">
                        <thead>
                          <tr>
                            <th colspan="2" rowspan="2">
                              {{'Defense Up'|translate:'app-ability'|escape}}
                            </th>
                            <th colspan="4">{{'Primary Ability'|translate:'app'|escape}}</th>
                          </tr>
                          <tr>
                            <th>0</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                          </tr>
                        </thead>
                        <tbody>
                          {{for $_defSub=0 to 9}}
                            <tr>
                            {{if $_defSub === 0}}
                              <th scope="row" rowspan="10">{{'Secondary Abilities'|translate:'app'|escape}}</th>
                            {{/if}}
                            <th scope="row">{{$_defSub|escape}}</th>
                            {{for $_defMain=0 to 3}}
                              {{$_damage = $effects->calcDamage($_attack->damage, $_defMain, $_defSub)}}
                              {{$_hit2kill = ceil(100 / $_damage)}}
                              <td class="{{if $_hit2kill > $_baseHit2Kill}}danger {{/if}}">
                                {{$_damage|number_format:1|escape}}
                              </td>
                            {{/for}}
                            </tr>
                          {{/for}}
                        </tbody>
                      </table>
                    {{/if}}
                  </td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->defensePct}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Defense'|translate:'app-gearstat'|escape}}</th>
                  <td>{{($_tmp * 100)|number_format:1|escape}} %</td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->inkUsePctMain}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Ink Usage(Main)'|translate:'app-gearstat'|escape}}</th>
                  <td>{{($_tmp * 100)|number_format:1|escape}} %</td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->inkUsePctSub}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Ink Usage(Sub)'|translate:'app-gearstat'|escape}}</th>
                  <td>{{($_tmp * 100)|number_format:1|escape}} %</td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->inkRecoverySec}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Ink Recovery'|translate:'app-gearstat'|escape}}</th>
                  <td>
                    {{$_param = [
                        'sec' => $_tmp|number_format:2,
                        'pct' => (100 * $_tmp / 3)|number_format:1
                      ]}}
                    {{'{sec} seconds ({pct} %)'|translate:'app':$_param|escape}}
                  </td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->runSpeedPct}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Run Speed'|translate:'app-gearstat'|escape}}</th>
                  <td>{{($_tmp * 100)|number_format:1|escape}} %</td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->swimSpeedPct}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Swim Speed'|translate:'app-gearstat'|escape}}</th>
                  <td>{{($_tmp * 100)|number_format:1|escape}} % ({{$battle->weapon->name|default:'?'|translate:'app-weapon'|escape}})</td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->specialChargePoint}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Special Charge'|translate:'app-gearstat'|escape}}</th>
                  <td>{{$effects->specialChargePoint|round|escape}} p ({{$battle->weapon->special->name|default:''|translate:'app-special'|escape}})</td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->specialDurationSec}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Special Duration'|translate:'app-gearstat'|escape}}</th>
                  <td>
                    {{$_tmp2 = $effects->specialDurationCount}}
                    {{if $_tmp2 === null}}
                      {{$_param = [
                          'sec' => $_tmp|number_format:2
                        ]}}
                      {{'{sec} seconds'|translate:'app':$_param|escape}}
                    {{else}}
                      {{$_param = [
                          'sec' => $_tmp|number_format:2,
                          'cnt' => $_tmp2
                        ]}}
                      {{'{sec} seconds, {cnt} times'|translate:'app':$_param|escape}}
                    {{/if}}
                    &#32;({{$battle->weapon->special->name|default:''|translate:'app-special'|escape}})
                  </td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->specialLossPct}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Special Save'|translate:'app-gearstat'|escape}}</th>
                  <td>
                    {{$_param = [
                        'pct' => ($_tmp * 100)|number_format:1
                      ]}}
                    {{'{pct} % loss'|translate:'app':$_param|escape}} ({{$battle->weapon->name|translate:'app-weapon'|escape}})
                  </td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->respawnSec}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Respawn'|translate:'app-gearstat'|escape}}</th>
                  <td>
                    {{$_param = [
                        'sec' => $_tmp|number_format:2
                      ]}}
                    {{'{sec} seconds'|translate:'app':$_param|escape}}
                  </td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->superJumpSecs}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Jump'|translate:'app-gearstat'|escape}}</th>
                  <td>
                    <span class="auto-tooltip" title="{{'Prepare'|translate:'app-gearstat'|escape}}">
                      {{$_param = ['sec' => $_tmp.prepare|number_format:2]}}
                      {{'{sec} seconds'|translate:'app':$_param|escape}}
                    </span>
                    &#32;+&#32;
                    <span class="auto-tooltip" title="{{'Ascent'|translate:'app-gearstat'|escape}}">
                      {{$_param = ['sec' => $_tmp.pullup|number_format:2]}}
                      {{'{sec} seconds'|translate:'app':$_param|escape}}
                    </span>
                    &#32;+&#32;
                    <span class="auto-tooltip" title="{{'Descent'|translate:'app-gearstat'|escape}}">
                      {{$_param = ['sec' => $_tmp.pulldown|number_format:2]}}
                      {{'{sec} seconds'|translate:'app':$_param|escape}}
                    </span>
                    &#32;+&#32;
                    <span class="auto-tooltip" title="{{'Stiffen'|translate:'app-gearstat'|escape}}">
                      {{$_param = ['sec' => $_tmp.rigid|number_format:2]}}
                      {{'{sec} seconds'|translate:'app':$_param|escape}}
                    </span>
                    &#32;=&#32;
                    {{$_param = [
                        'sec' => ($_tmp.prepare + $_tmp.pullup + $_tmp.pulldown + $_tmp.rigid)|number_format:2
                      ]}}
                    {{'{sec} seconds'|translate:'app':$_param|escape}}
                  </td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->bombThrowPct}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Bomb Throw'|translate:'app-gearstat'|escape}}</th>
                  <td>{{($_tmp * 100)|number_format:1|escape}} %</td>
                </tr>
              {{/if}}

              {{$_tmp = $effects->markingPct}}
              {{if $_tmp !== null}}
                <tr>
                  <th>{{'Echolocator'|translate:'app-gearstat'|escape}}</th>
                  <td>{{($_tmp * 100)|number_format:1|escape}} %</td>
                </tr>
              {{/if}}
            </tbody>
          </table>
          <p class="text-right" style="font-size:10px;line-height:1.1">
            [{{$effects->calculatorVersion|escape}}]<br>
            Powered by <a href="http://wikiwiki.jp/splatoon2ch/?%A5%AE%A5%A2%A5%D1%A5%EF%A1%BC%B8%A1%BE%DA">ギアパワー検証 - スプラトゥーン(Splatoon) for 2ch Wiki*</a>
          </p>
        {{/if}}
        {{if !$app->user->isGuest && $app->user->identity->id == $user->id && $hasExtendedData}}
          <p class="text-right">
            <a href="{{url route="show/edit-battle" screen_name=$user->screen_name battle=$battle->id}}" class="btn btn-default">
              {{'Edit'|translate:'app'|escape}}
            </a>
          </p>
        {{/if}}
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerCss}}{{literal}}
#battle th{width:15em}
@media(max-width:30em){#battle th{width:auto}}
.image-container{margin-bottom:15px}
{{/literal}}{{/registerCss}}
