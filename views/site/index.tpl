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

    {{if false}}
      <p class="bg-danger" style="padding:15px;border-radius:10px">
        {{if $app->language === 'ja-JP'}}
          2017-05-25 03:00 (日本時間) 頃から stat.ink のサーバメンテナンスを行います。<br>
          メンテナンス中は stat.ink への投稿も含め、全てのアクセスが行えません。<br>
          また、メンテナンス終了時にサーバのIPアドレスが変更になります。<br>
          これに伴い、環境によってはしばらくアクセスできなくなる可能性があります。<br>
          （アプリケーションやブラウザ、OSを再起動すると回復する可能性があります）
        {{else}}
          We scheduled server maintenance at 2017-05-25 03:00 <a href="https://en.wikipedia.org/wiki/Japan_Standard_Time">JST</a>.<br>
          (2017-05-24 18:00 UTC, 2017-05-24 2:00pm EDT, 2017-05-24 11:00am PDT)<br>
          The service will be shut down and will not be accessed.<br>
          It will take several hours for the maintenance.<br>
          Sorry for inconvenience.
        {{/if}}
      </p>
    {{/if}}
    {{if false}}
      <p class="bg-danger" style="padding:15px;border-radius:10px">
        <a href="https://testfire2.stat.ink/">Splatoon 2 試射会用の記録サイトを、技術テストを兼ねて運用します。</a><br>
        Splatoon 2 試射会対応の<a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec2">イカレコ 2</a> はこの記録サイトに対応しています。<br>
        対応するIkaLogが登場するかは未定です。<br>
        <br>
        データベースが完全に別になっているため、会員登録から行ってください。<br>
        （対応するIkaLogがリリースされた場合、画像認識のためのデータ収集サイトを兼ねることになります）
      </p>
    {{/if}}

    {{if $app->language === 'ja-JP'}}
      <p class="bg-danger" style="padding:15px;border-radius:10px">
        Splatoon 2 前夜祭登録機能に対応しました。右上の登録ボタンからご利用ください。<br>
        まだ Splatoon 2 対応は半端な状態で、仮対応です。<br>
        ナビゲーションや機能面で不完全な箇所が目立ちますがご容赦ください。
      </p>
      <p class="bg-warning" style="padding:15px;border-radius:10px">
        バトル登録機能をリリースしました。ログイン後、バトル登録ボタンから登録できます。<br>
        iOS等をご利用の方、PCでキャプチャボードを使用出来ない方、どうぞご利用ください。<br>
        (Androidをご利用の方には引き続きイカレコをオススメします)
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

    {{use class="app\models\Splatfest"}}
    {{$fest = Splatfest::findCurrentFest()
        ->joinWith('splatfestMaps.map')
        ->andWhere(['region_id' => $app->splatoonRegion])
        ->one()}}
    {{if $fest}}
      {{\app\assets\MapImageAsset::register($this)|@void}}
      <h2>
        {{'Splatfest'|translate:'app'|escape}}
      </h2>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <h3>
            {{'Turf War'|translate:'app-rule'|escape}}
          </h3>
          <ul class="battles fest-maps">
            {{foreach $fest->splatfestMaps as $_}}
              {{if $_ && $_->map}}
                <li>
                  <div class="thumbnail thumbnail-nawabari">
                    {{* ここ本当は night/ *}}
                    {{$mapFile = 'daytime/'|cat:$_->map->key:'.jpg'}}
                    <img src="{{$app->assetManager->getAssetUrl(
                        $app->assetManager->getBundle('app\assets\MapImageAsset'),
                        $mapFile
                      )}}">
                    <div class="caption row">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 omit">
                        {{$_->map->name|translate:'app-map'|escape}}
                      </div>
                    </div>
                  </div>
                </li>
              {{/if}}
            {{/foreach}}
          </ul>
        </div>
      </div>
      <p class="text-right" style="font-size:10px;line-height:1.1">
        勝率推計：
        <a href="{{url route="fest/view" region=$fest->region->key order=$fest->order}}">{{$app->name|escape}}の投稿情報</a>
        {{if $fest->region->key == 'jp'}}
          , <a href="https://fest.ink/{{$fest->order|escape:url}}">イカフェスレート</a>
        {{/if}}
      </p>
    {{else}}
      {{use class="app\models\PeriodMap"}}
      {{$stageInfo = PeriodMap::getSchedule()}}
      {{if $stageInfo->current->regular || $stageInfo->current->gachi}}
        {{\app\assets\MapImageAsset::register($this)|@void}}
        {{$timeFormat = '%H:%M'}}
        {{if $app->language == 'en-US'}}
          {{$timeFormat = '%l:%M %p'}}
        {{/if}}
        <h2>
          <span class="hidden-xs">{{'Current Stages'|translate:'app'|escape}}</span>
          {{if $stageInfo->current->t}}
            {{$t = $stageInfo->current->t}}
            <span class="hidden-xs">&#32;[</span>
            {{$t.0|date_format:$timeFormat|escape}}-{{$t.1|date_format:$timeFormat|escape}}
            <span class="hidden-xs">]</span>
          {{/if}}
          {{if $stageInfo->next->regular || $stageInfo->next->gachi}}
            &#32;<button id="show-next-stage" type="button" class="btn btn-default">
              {{if $stageInfo->next->t}}
                {{$t = $stageInfo->next->t}}
                {{$t.0|date_format:$timeFormat|escape}}-{{$t.1|date_format:$timeFormat|escape}}
              {{else}}
                {{'Next Stage'|translate:'app'|escape}}
              {{/if}}
            </button>
            {{registerJs}}
              $('#show-next-stage').click(function(){
                var $this = $(this);
                var $next = $('#next-stage');
                $.smoothScroll({
                  offset: -60,
                  scrollTarget: $next,
                  beforeScroll: function () {
                    $next.show('fast');
                    $this.hide();
                  }
                });
                return false;
              });
            {{/registerJs}}
          {{/if}}
        </h2>
        <p class="text-right" style="margin:0">
          <!--a href="http://graystar0907.wixsite.com/bukiicons" rel="external"-->
            {{"Weapon icons were created by {0}."|translate:'app':'Stylecase'|escape}}
          <!--/a-->
        </p>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            {{include
                file="@app/views/site/_index_stage.tpl"
                rule=$stageInfo->current->regular.0->rule
                stages=$stageInfo->current->regular
            }}
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            {{include
                file="@app/views/site/_index_stage.tpl"
                rule=$stageInfo->current->gachi.0->rule
                stages=$stageInfo->current->gachi
            }}
          </div>
        </div>
        {{if $stageInfo->next->regular && $stageInfo->next->gachi}}
          {{registerCss}}
            #next-stage{display:none}
          {{/registerCss}}
          <div id="next-stage">
            <h2>
              <span class="hidden-xs">{{'Next Stage'|translate:'app'|escape}}</span>
              {{if $stageInfo->next->t}}
                {{$t = $stageInfo->next->t}}
                <span class="hidden-xs">&#32;[</span>
                {{$t.0|date_format:$timeFormat|escape}}-{{$t.1|date_format:$timeFormat|escape}}
                <span class="hidden-xs">]</span>
              {{/if}}
            </h2>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                {{include
                    file="@app/views/site/_index_stage.tpl"
                    rule=$stageInfo->next->regular.0->rule
                    stages=$stageInfo->next->regular
                }}
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                {{include
                    file="@app/views/site/_index_stage.tpl"
                    rule=$stageInfo->next->gachi.0->rule
                    stages=$stageInfo->next->gachi
                }}
              </div>
            </div>
          </div>
        {{/if}}
      {{/if}}
    {{/if}}

    {{if $ident}}
      {{$battles = CombinedBattles::getUserRecentBattles($ident, 12)}}
      {{if $battles}}
        {{$title = "{0}'s Battles"|translate:'app':$ident->name}}
        <h2>
          <a href="{{url route="show/user" screen_name=$ident->screen_name}}">
            {{$title|escape}}
          </a>
        </h2>
        {{BattleListWidget::widget(['models' => $battles])}}
      {{/if}}
    {{/if}}

    <h2>
      {{'Active Players'|translate:'app'|escape}}
    </h2>
    <p class="text-right" style="margin:0">
      <a href="{{url route="site/users"}}">
        {{'Show All Players'|translate:'app'|escape}}
      </a>
    </p>
    {{BattleListWidget::widget(['models' => $active])}}

    <h2>
      {{'Recent Battles'|translate:'app'|escape}}
    </h2>
    {{BattleListWidget::widget(['models' => CombinedBattles::getRecentBattles(100)])}}
  </div>
{{/strip}}
