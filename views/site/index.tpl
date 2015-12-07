{{strip}}
  {{set layout="main.tpl"}}
  {{use class="app\models\Battle"}}
  <div class="container">
    <h1>
      {{$app->name|escape}}
    </h1>
    <p>
      {{'Staaaay Fresh!'|translate:'app'|escape}}<br>
      {{if $app->user->isGuest}}
        {{$ident = null}}
        <a href="{{url route="user/register"}}">{{'Join us'|translate:'app'|escape}}</a>
      {{else}}
        {{$ident = $app->user->identity}}
        <a href="{{url route="show/user" screen_name=$ident->screen_name}}">{{'Your Battles'|translate:'app'|escape}}</a>
      {{/if}} | <a href="{{url route="site/start"}}">{{"What's this?"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="site/faq"}}">{{"FAQ"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="entire/users"}}">{{"Stat: User Activity"|translate:'app'|escape}}</a><br>

      <a href="{{url route="entire/kd-win"}}">{{"Stat: K/D vs WP"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="entire/knockout"}}">{{"Stat: Knockout Rate"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="entire/weapons"}}">{{"Stat: Weapons"|translate:'app'|escape}}</a>
    </p>
    <p>
      <a href="{{url route="site/privacy"}}#image">IkaLogへの画像提供について</a>
    </p>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    {{use class="app\models\PeriodMap"}}
    {{$stageInfo = PeriodMap::getSchedule()}}
    {{if $stageInfo->current->regular || $stageInfo->current->gachi}}
      {{\app\assets\MapImageAsset::register($this)|@void}}
      <h2>
        {{'Current Stage'|translate:'app'|escape}}
        {{if $stageInfo->next->regular || $stageInfo->next->gachi}}
          &#32;<button id="show-next-stage" type="button" class="btn btn-default">{{'Next Stage'|translate:'app'|escape}}</button>
          {{registerJs}}
            $('#show-next-stage').click(function(){
              $('#next-stage').show('fast');
              $(this).hide();
            });
          {{/registerJs}}
        {{/if}}
      </h2>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
          <h3>
            {{$stageInfo->current->regular.0->rule->name|translate:'app-rule'|escape}}
          </h3>
          <ul class="battles maps">
            {{foreach $stageInfo->current->regular as $_}}
              <li>
                <div class="battle">
                  <div class="battle-image">
                    {{$mapFile = 'daytime/'|cat:$_->map->key:'.jpg'}}
                    <img src="{{$app->assetManager->getAssetUrl(
                        $app->assetManager->getBundle('app\assets\MapImageAsset'),
                        $mapFile
                      )}}">
                  </div>
                  <div class="battle-data row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 omit">
                      {{$_->map->name|translate:'app-map'|escape}}
                    </div>
                  </div>
                </div>
              </li>
            {{/foreach}}
          </ul>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
          <h3>
            {{$stageInfo->current->gachi.0->rule->name|translate:'app-rule'|escape}}
          </h3>
          <ul class="battles maps">
            {{foreach $stageInfo->current->gachi as $_}}
              <li>
                <div class="battle">
                  <div class="battle-image">
                    {{$mapFile = 'daytime/'|cat:$_->map->key:'.jpg'}}
                    <img src="{{$app->assetManager->getAssetUrl(
                        $app->assetManager->getBundle('app\assets\MapImageAsset'),
                        $mapFile
                      )}}">
                  </div>
                  <div class="battle-data row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 omit">
                      {{$_->map->name|translate:'app-map'|escape}}
                    </div>
                  </div>
                </div>
              </li>
            {{/foreach}}
          </ul>
        </div>
      </div>
      {{if $stageInfo->next->regular && $stageInfo->next->gachi}}
        {{registerCss}}
          #next-stage{display:none}
        {{/registerCss}}
        <div id="next-stage">
          <h2>
            {{'Next Stage'|translate:'app'|escape}}
          </h2>
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
              <h3>
                {{$stageInfo->next->regular.0->rule->name|translate:'app-rule'|escape}}
              </h3>
              <ul class="battles maps">
                {{foreach $stageInfo->next->regular as $_}}
                  <li>
                    <div class="battle">
                      <div class="battle-image">
                        {{$mapFile = 'daytime/'|cat:$_->map->key:'.jpg'}}
                        <img src="{{$app->assetManager->getAssetUrl(
                            $app->assetManager->getBundle('app\assets\MapImageAsset'),
                            $mapFile
                          )}}">
                      </div>
                      <div class="battle-data row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 omit">
                          {{$_->map->name|translate:'app-map'|escape}}
                        </div>
                      </div>
                    </div>
                  </li>
                {{/foreach}}
              </ul>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
              <h3>
                {{$stageInfo->next->gachi.0->rule->name|translate:'app-rule'|escape}}
              </h3>
              <ul class="battles maps">
                {{foreach $stageInfo->next->gachi as $_}}
                  <li>
                    <div class="battle">
                      <div class="battle-image">
                        {{$mapFile = 'daytime/'|cat:$_->map->key:'.jpg'}}
                        <img src="{{$app->assetManager->getAssetUrl(
                            $app->assetManager->getBundle('app\assets\MapImageAsset'),
                            $mapFile
                          )}}">
                      </div>
                      <div class="battle-data row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 omit">
                          {{$_->map->name|translate:'app-map'|escape}}
                        </div>
                      </div>
                    </div>
                  </li>
                {{/foreach}}
              </ul>
            </div>
          </div>
        </div>
      {{/if}}
      <p class="text-right" style="font-size:0.333rem;line-height:1.1">
        Powered by <a href="http://splapi.retrorocket.biz/" target="_blank" rel="nofollow">
          スプラトゥーンのステージじょうほうがとれるやつ
        </a>
      </p>
    {{/if}}

    {{if $ident}}
      {{$battles = $ident->getBattles()
        ->with(['user', 'rule', 'map', 'battleImageResult'])
        ->limit(12)
        ->all()}}
      {{if $battles}}
        {{$name = '{0}-san'|translate:'app':$ident->name}}
        {{$title = "{0}'s Battle"|translate:'app':$name}}
        <h2>
          <a href="{{url route="show/user" screen_name=$ident->screen_name}}">
            {{$title|escape}}
          </a>
        </h2>
        {{include file="@app/views/includes/battle_thumb_list.tpl" battles=$battles}}
      {{/if}}
    {{/if}}

    <h2>
      {{'Active Players'|translate:'app'|escape}}
    </h2>
    <p class="right" style="margin:0">
      <a href="{{url route="site/users"}}">
        {{'Display Everyone'|translate:'app'|escape}}
      </a>
    </p>
    {{include file="@app/views/includes/battle_thumb_list.tpl" battles=$active}}

    <h2>
      {{'Recent Battles'|translate:'app'|escape}}
    </h2>
    {{$battles = Battle::find()
        ->with(['user', 'rule', 'map', 'battleImageResult'])
        ->hasResultImage()
        ->limit(100)
        ->all()}}
    {{include file="@app/views/includes/battle_thumb_list.tpl" battles=$battles}}
  </div>
{{/strip}}
