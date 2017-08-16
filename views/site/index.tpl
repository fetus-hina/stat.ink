{{strip}}
  {{set layout="main.tpl"}}
  {{use class="app\components\helpers\CombinedBattles"}}
  {{use class="app\components\widgets\battle\BattleListWidget"}}
  {{use class="app\models\Battle"}}
  {{use class="app\models\User"}}
  <div class="container">
    <p class="text-right" style="margin-bottom:0">
      Switch Language:&#32;
      {{use class="app\models\Language"}}
      {{foreach Language::find()->orderBy('name ASC')->asArray()->all() as $lang}}
        {{if !$lang@first}}
          &#32;/&#32;
        {{/if}}
        <a href="javascript:;" data-lang="{{$lang.lang|escape}}" class="language-change auto-tooltip" style="white-space:nowrap" title="Switch to {{$lang.name_en|escape}}">
          <span class="flag-icon flag-icon-{{$lang.lang|substr:3:2|strtolower|escape}}"></span>&#32;{{$lang.name|escape}}
        </a>
      {{/foreach}}
    </p>
    <p class="text-right" style="margin-bottom:0">
      {{\app\assets\CounterAsset::register($this)|@void}}
      <span style="white-space:nowrap">
        Users: <span class="dseg-counter" data-type="users">{{User::getRoughCount()|default:'?'|escape}}</span>,
      </span>&#32;<span style="white-space:nowrap">
        Battles: <span class="dseg-counter" data-type="battles">{{Battle::getTotalRoughCount()|default:'?'|escape}}</span>
      </span>
    </p>
    {{if $enableAnniversary}}
      <p class="text-center" style="font-size:150%">
        <span class="emoji">&#x1F382;</span>&#32;
        stat.ink: Happy First Anniversary!&#32;
        {{if $app->language === 'ja-JP'}}
          9/25
        {{else}}
          25th Sept.
        {{/if}}&#32;
        <span class="emoji">&#x1F382;</span>
      </p>
    {{/if}}

    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-8 col-lg-9">
        {{\app\assets\PaintballAsset::register($this)|@void}}
        <h1 class="paintball" style="font-size:42px;margin-top:0">
          {{$app->name|escape}}
        </h1>
        <p>
          {{'Staaaay Fresh!'|translate:'app'|escape}}
        </p>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
        {{$_path = Yii::getAlias('@app/views/includes/sponsored.tpl')}}
        {{if $_path|file_exists}}
          {{include file=$_path}}
        {{/if}}
      </div>
    </div>

    {{if $app->language === 'ja-JP'}}
      <p class="bg-warning" style="padding:15px;border-radius:10px">
        バトル登録機能をリリースしました。ログイン後、バトル登録ボタンから登録できます。<br>
        iOS等をご利用の方、PCでキャプチャボードを使用出来ない方、どうぞご利用ください。<br>
        (Androidをご利用の方には引き続きイカレコをオススメします)
      </p>
    {{elseif $app->language|substr:0:2 === 'fr'}}
      <p class="bg-warning" style="padding:15px;border-radius:10px">
        French language support is really limited at this time.<br>
        Only proper nouns translated. (e.g. weapons, stages)<br>
        We need your support!
      </p>
    {{/if}}
    <p>
      {{if $app->user->isGuest}}
        {{$ident = null}}
        <a href="{{url route="user/register"}}">{{'Join us'|translate:'app'|escape}}</a>
      {{else}}
        {{$ident = $app->user->identity}}
        <a href="{{url route="show/user" screen_name=$ident->screen_name}}">{{'Your Battles'|translate:'app'|escape}}</a>
      {{/if}} | <a href="{{url route="site/start"}}">{{'Getting Started'|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="site/faq"}}">{{"FAQ"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="entire/users"}}">{{"Stats: User Activity"|translate:'app'|escape}}</a><br>

      <a href="{{url route="entire/kd-win"}}">{{"Stats: K/D vs Win %"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="entire/knockout"}}">{{"Stats: Knockout Ratio"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="entire/weapons"}}">{{"Stats: Weapons"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="stage/index"}}">{{"Stats: Stages"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="download-stats/index"}}">統計情報ダウンロード(開発中)</a>
    </p>
    <p>
      <a href="{{url route="site/color"}}">{{'About support for color-blindness'|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="site/privacy"}}#image">{{'About image sharing with the IkaLog team'|translate:'app'|escape}}</a>
    </p>

    {{SnsWidget}}

    {{use class="app\models\BlogEntry"}}
    {{$blogEntries = BlogEntry::find()
        ->orderBy('[[at]] DESC')
        ->limit(3)
        ->asArray()
        ->all()}}
    {{if $blogEntries}}
      <p class="bg-success" style="padding:15px;border-radius:10px">
        {{foreach $blogEntries as $entry}}
          {{if !$entry@first}} | {{/if}}
          <span style="white-space:nowrap">
            <a href="{{$entry.url|escape}}">
              {{$entry.title|escape}}
            </a>&#32;
            ({{$entry.at|active_reltime}})
          </span>
        {{/foreach}}
      </p>
    {{/if}}

    {{* ステージ情報 *}}
    <h2>{{'Schedule'|translate:'app'|escape}}</h2>
    <ul class="nav nav-tabs" role="tablist" id="schedule-tab">
      <li role="presentation" class="active"><a href="#schedule-spl2" data-toggle="tab">Splatoon 2</a></li>
      <li role="presentation"><a href="#schedule-spl1" data-toggle="tab">Splatoon</a></li>
    </ul>
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="schedule-spl2">
        {{* Splatoon 2 ステージ情報 *}}
        {{$this->render('_index_stages2')}}
      </div>
      <div role="tabpanel" class="tab-pane" id="schedule-spl1">
        {{* Splatoon 1 ステージ情報 *}}
        {{include file="@app/views/site/_index_stages1.tpl"}}
      </div>
    </div>

    {{if $ident}}
      {{$battles = CombinedBattles::getUserRecentBattles($ident, 12)}}
      {{if $battles}}
        {{$title = "{0}'s Battles"|translate:'app':$ident->name}}
        <h2>
          <a href="{{url route="show-user/profile" screen_name=$ident->screen_name}}">
            {{$title|escape}}
          </a>
        </h2>
        {{BattleListWidget::widget(['models' => $battles])}}
      {{/if}}
    {{/if}}

    {{*
    <h2>
      {{'Active Players'|translate:'app'|escape}}
    </h2>
    {{BattleListWidget::widget(['models' => $active])}}
    *}}

    <h2>
      {{'Recent Battles'|translate:'app'|escape}}
    </h2>
    {{BattleListWidget::widget(['models' => CombinedBattles::getRecentBattles(100)])}}
  </div>
{{/strip}}
