{{strip}}
  {{*\app\assets\GearCalcAsset::register($this)|@void*}}
  {{set layout="main.tpl"}}
  {{use class="yii\helpers\Url"}}
  {{$user = $battle->user}}
  {{$canonicalUrl = Url::to(['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id], true)}}
  {{$title = "Results of {0}'s Battle"|translate:'app':$user->name}}
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
    {{$tmp = $battle->rule->name|translate:'app-rule2'}}
    {{$summary = $summary|cat:$tmp:' | '}}
  {{/if}}
  {{if $battle->map}}
    {{$tmp = $battle->map->name|translate:'app-map2'}}
    {{$summary = $summary|cat:$tmp:' | '}}
  {{/if}}
  {{if $battle->is_win !== null}}
    {{if $battle->is_win}}
      {{$tmp = 'Won'|translate:'app'}}
    {{else}}
      {{$tmp = 'Lost'|translate:'app'}}
    {{/if}}
    {{$summary = $summary|cat:$tmp:' | '}}
  {{/if}}
  {{$summary = $summary|rtrim:'| '}}
  {{$this->registerMetaTag(['name' => 'twitter:description', 'content' => $summary])|@void}}

  {{if $battle->previousBattle}}
    {{$_url = Url::to(['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->previousBattle->id], true)}}
    {{$this->registerLinkTag(['rel' => 'prev', 'href' => $_url])|@void}}
  {{/if}}
  {{if $battle->nextBattle}}
    {{$_url = Url::to(['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->nextBattle->id], true)}}
    {{$this->registerLinkTag(['rel' => 'next', 'href' => $_url])|@void}}
  {{/if}}

  {{use class="app\models\Special2"}}
  {{$specials = Special2::find()->asArray()->all()}}

  <div class="container">
    <h1>
      {{$_url = Url::to(['show-v2/user', 'screen_name' => $user->screen_name])}}
      {{$name = $user->name|escape}}
      {{$name = '<a href="%s">%s</a>'|sprintf:$_url:$name}}
      {{"Results of {0}'s Battle"|translate:'app':$name}}
    </h1>

{{*
    {{if $battle->agent}}
      {{use class="app\components\helpers\IkalogVersion"}}
      {{if IkalogVersion::isOutdated($battle)}}
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
*}}

    {{SnsWidget}}

    {{if $battle->battleImageJudge || $battle->battleImageResult}}
      {{\app\assets\SwipeboxRunnerAsset::register($this)|@void}}
      <div class="row">
        {{if $battle->battleImageJudge}}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
            <a href="{{$battle->battleImageJudge->url|escape}}" class="swipebox">
              <img src="{{$battle->battleImageJudge->url|escape}}" style="max-width:100%;height:auto">
            </a>
          </div>
        {{/if}}
        {{if $battle->battleImageResult}}
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 image-container">
            <a href="{{$battle->battleImageResult->url|escape}}" class="swipebox">
              <img src="{{$battle->battleImageResult->url|escape}}" style="max-width:100%;height:auto">
            </a>
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
                <a href="{{url route="/show-v2/battle" screen_name=$user->screen_name battle=$battle->previousBattle->id}}" class="btn btn-default">
                  <span class="fa fa-angle-double-left left"></span>{{'Prev. Battle'|translate:'app'|escape}}
                </a>
              </div>
            {{/if}}
            {{if $battle->nextBattle}}
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pull-right text-right">
                <a href="{{url route="/show-v2/battle" screen_name=$user->screen_name battle=$battle->nextBattle->id}}" class="btn btn-default">
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
            {{if $battle->mode}}
              <tr>
                <th>
                  {{'Game Mode'|translate:'app'|escape}}
                </th>
                <td>
                  {{$battle->mode->name|translate:'app-rule2'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->lobby}}
              <tr>
                <th>
                  {{'Lobby'|translate:'app'|escape}}
                </th>
                <td>
                  {{$battle->lobby->name|translate:'app-rule2'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->rule}}
              <tr>
                <th>
                  {{'Mode'|translate:'app'|escape}}&#32;
                  <!--a href="{{url route="/show-v2/user-stat-by-rule" screen_name=$user->screen_name}}">
                    <span class="fa fa-pie-chart"></span>
                  </a-->
                </th>
                <td>
                  <!--a href="{{url route="/show-v2/user" screen_name=$user->screen_name}}?filter[rule]={{$battle->rule->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a-->&#32;
                  {{$battle->rule->name|translate:'app-rule'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->map}}
              <tr>
                <th>
                  {{'Stage'|translate:'app'|escape}}&#32;
                  <!--a href="{{url route="/show-v2/user-stat-by-map" screen_name=$user->screen_name}}">
                    <span class="fa fa-pie-chart"></span>
                  </a-->
                </th>
                <td>
                  <!--a href="{{url route="/show-v2/user" screen_name=$user->screen_name}}?filter[map]={{$battle->map->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a-->&#32;
                  {{$battle->map->name|translate:'app-map2'|escape}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->weapon}}
              <tr>
                <th>{{'Weapon'|translate:'app'|escape}}</th>
                <td>
                  <!--a href="{{url route="/show-v2/user" screen_name=$user->screen_name}}?filter[weapon]={{$battle->weapon->key|escape:url}}">
                    <span class="fa fa-search"></span>
                  </a-->&#32;
                  {{$battle->weapon->name|translate:'app-weapon2'|escape}}
                  &#32;(
                  {{$battle->weapon->subweapon->name|default:'?'|translate:'app-subweapon2'|escape}} /&#32;
                  {{$battle->weapon->special->name|default:'?'|translate:'app-special2'|escape}}
                  )
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
            {{if $battle->is_win !== null}}
              <tr>
                <th>{{'Result'|translate:'app'|escape}}</th>
                <td>
{{*
                  {{if $battle->isGachi && $battle->is_knock_out !== null}}
                    {{if $battle->is_knock_out}}
                      <span class="label label-info">
                        {{'Knockout'|translate:'app'|escape}}
                      </span>
                    {{else}}
                      <span class="label label-warning">
                        {{'Time is up'|translate:'app'|escape}}
                      </span>
                    {{/if}}
                    &#32;
                  {{/if}}
*}}
                  {{if $battle->is_win === true}}
                    <span class="label label-success">
                      {{'Won'|translate:'app'|escape}}
                    </span>
                  {{elseif $battle->is_win === false}}
                    <span class="label label-danger">
                      {{'Lost'|translate:'app'|escape}}
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
                <th>{{'Kills / Deaths'|translate:'app'|escape}}</th>
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
              <tr>
                <th>{{'Kill Rate'|translate:'app'|escape}}</th>
                <td>
                  {{$_ = $battle->kill_rate}}
                  {{if $_ === null}}
                    {{'N/A'|translate:'app'|escape}}
                  {{else}}
                    {{$_|escape}}%
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            {{if $battle->max_kill_combo !== null}}
              <tr>
                <th>{{'Max Kill Combo'|translate:'app'|escape}}</th>
                <td>{{$battle->max_kill_combo|escape}}
              </tr>
            {{/if}}
            {{if $battle->max_kill_streak !== null}}
              <tr>
                <th>{{'Max Kill Streak'|translate:'app'|escape}}</th>
                <td>{{$battle->max_kill_streak|escape}}
              </tr>
            {{/if}}
{{*
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
*}}
            {{if $battle->my_point}}
              <tr>
                <th>{{'Turf Inked + Bonus'|translate:'app'|escape}}</th>
                <td>
                  {{$_inked = $battle->inked}}
                  {{if $_inked !== null}}
                    {{$_inked|number_format|escape}}
                    {{if $battle->is_win && $battle->bonus}}
                      &#32;+ {{$battle->bonus->bonus|number_format|escape}}
                    {{/if}}
                  {{else}}
                    {{$battle->my_point|number_format|escape}}
                  {{/if}}
                  &#32;P
                </td>
              </tr>
            {{/if}}
            {{if $battle->my_team_point || $battle->his_team_point}}
              <tr>
                <th>{{'My Team Score'|translate:'app'|escape}}</th>
                <td>
                  {{$battle->my_team_point|default:'?'|escape}} P (
                  {{if $battle->my_team_percent === null}}
                    ?
                  {{else}}
                    {{$battle->my_team_percent|number_format:1|escape}}
                  {{/if}}
                  %)
                </td>
              </tr>
              <tr>
                <th>{{'Their Team Score'|translate:'app'|escape}}</th>
                <td>
                  {{$battle->his_team_point|default:'?'|escape}} P (
                  {{if $battle->his_team_percent === null}}
                    ?
                  {{else}}
                    {{$battle->his_team_percent|number_format:1|escape}}
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
{{*
            {{if $battle->headgear || $battle->clothing || $battle->shoes}}
              <tr>
                <th>
                  {{'Gear'|translate:'app'|escape}}
                  {{if $battle->battleImageGear}}
                    {{\app\assets\SwipeboxRunnerAsset::register($this)|@void}}
                    &#32;
                    <a href="{{$battle->battleImageGear->url}}" class="swipebox">
                      <span class="fa fa-picture-o"></span>
                    </a>
                  {{/if}}
                </th>
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
*}}
            {{$_editable = (!$app->user->isGuest && $app->user->identity->id == $battle->user_id)}}
            {{$_editable = false}}
            {{if $battle->link_url != '' || $_editable}}
              {{if $_editable}}
                {{\app\assets\BattleEditAsset::register($this)|@void}}
              {{/if}}
              <tr>
                <th>{{'Link'|translate:'app'|escape}}</th>
                <td id="link-cell">
                  <div id="link-cell-display" data-post="{{url route="api-internal/patch-battle" id=$battle->id}}" data-url="{{$battle->link_url|escape}}">
                    {{if $battle->link_url != ''}}
                      <a href="{{$battle->link_url|escape}}" rel="nofollow" class="swipebox">
                        {{$battle->link_url|decode_idn|escape}}
                      </a>&#32;
                    {{/if}}
                    {{if $_editable}}
                      <button id="link-cell-start-edit" class="btn btn-default btn-xs" disabled>
                        <span class="fa fa-pencil left"></span>{{'Edit'|translate:'app'|escape}}
                      </button>
                    {{/if}}
                  </div>
                  {{if $_editable}}
                    <div id="link-cell-edit" style="display:none">
                      <div class="form-group-sm">
                        <input type="url" value="" id="link-cell-edit-input" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                      </div>
                      <button type="button" id="link-cell-edit-apply" class="btn btn-primary btn-xs" disabled data-error="{{'Could not be updated.'|translate:'app'|escape}}">
                        {{'Apply'|translate:'app'|escape}}
                      </button>
                    </div>
                  {{/if}}
                </td>
              </tr>
            {{/if}}
            <tr>
              <th>{{'Battle Start'|translate:'app'|escape}}</th>
              <td>{{$battle->start_at|as_datetime|escape}}</td>
            </tr>
            <tr>
              <th>{{'Battle End'|translate:'app'|escape}}</th>
              <td>{{$battle->end_at|as_datetime|escape}}</td>
            </tr>
            <tr>
              <th>{{'Data Sent'|translate:'app'|escape}}</th>
              <td>{{$battle->created_at|as_datetime|escape}}</td>
            </tr>
            {{if $battle->agent}}
              <tr>
                <th>{{'User Agent'|translate:'app'|escape}}</th>
                <td>
                  {{$_link = $battle->agent->productUrl}}
                  {{if $_link}}<a href="{{$_link|escape}}" target="_blank" rel="nofollow">{{/if}}
                  {{$battle->agent->name|escape}}
                  {{if $_link}}</a>{{/if}}
                  &#32;/&#32;
                  {{$_link = $battle->agent->versionUrl}}
                  {{if $_link}}<a href="{{$_link|escape}}" target="_blank" rel="nofollow">{{/if}}
                  {{$battle->agent->version|escape}}
                  {{if $_link}}</a>{{/if}}
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
                {{if $battle->version}}
                  {{$battle->version->name|escape}}
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
{{*
        {{if !$app->user->isGuest && $app->user->identity->id == $user->id}}
          <p class="text-right">
            <a href="{{url route="/show-v2/edit-battle" screen_name=$user->screen_name battle=$battle->id}}" class="btn btn-default">
              {{'Edit'|translate:'app'|escape}}
            </a>
          </p>
        {{/if}}
*}}
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{include file="@app/views/includes/user-miniinfo2.tpl" user=$user}}
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
