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
        <a href="{{url route="user/register"}}">{{'Join us'|translate:'app'|escape}}</a>
      {{else}}
        {{$ident = $app->user->identity}}
        <a href="{{url route="show/user" screen_name=$ident->screen_name}}">{{'Your Battles'|translate:'app'|escape}}</a>
      {{/if}} | <a href="{{url route="site/start"}}">{{"What's this?"|translate:'app'|escape}}</a>
    </p>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    {{$battles = Battle::find()
        ->with(['user', 'rule', 'map'])
        ->hasResultImage()
        ->limit(100)
        ->all()}}
    <ul class="battles">
      {{$imagePlaceholder = $app->assetManager->getAssetUrl(
          $app->assetManager->getBundle('app\assets\AppAsset'),
          'no-image.png'
        )}}
      {{\app\assets\JqueryLazyloadAsset::register($this)|@void}}
      {{foreach $battles as $battle}}
        <li>
          <div class="battle">
            <div class="battle-image">
              {{$rule = $battle->rule->name|default:'?'|translate:'app-rule'}}
              {{$map = $battle->map->name|default:'?'|translate:'app-map'}}
              {{if $battle->is_win === null}}
                {{$result = '?'}}
              {{elseif $battle->is_win}}
                {{$result = 'WON'|translate:'app'}}
              {{else}}
                {{$result = 'LOST'|translate:'app'}}
              {{/if}}
              {{$description = "%s / %s / %s"|sprintf:$rule:$map:$result}}
              <a href="{{url route="show/battle" screen_name=$battle->user->screen_name battle=$battle->id}}">
                <img src="{{$imagePlaceholder|escape}}" class="lazyload auto-tooltip" data-original="{{$battle->battleImageResult->url|default:''|escape}}" title="{{$description|escape}}">
                {{registerJs}}jQuery('img.lazyload').lazyload();{{/registerJs}}
              </a>
            </div>
            <div class="battle-data row">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <a href="{{url route="show/user" screen_name=$battle->user->screen_name}}">{{$battle->user->name|escape}}</a>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 time">
                {{if $battle->end_at}}
                  {{$t = $battle->end_at|date_format:'%Y-%m-%d %H:%M %Z'}}
                  <a href="{{url route="show/battle" screen_name=$battle->user->screen_name battle=$battle->id}}" title="{{$t|escape}}" class="auto-tooltip">
                    {{$battle->end_at|relative_time|escape}}
                  </a>
                {{/if}}
              </div>
            </div>
          </div>
        </li>
      {{/foreach}}
    </ul>
  </div>
{{/strip}}
