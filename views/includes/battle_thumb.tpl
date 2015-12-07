{{strip}}
  {{\app\assets\JqueryLazyloadAsset::register($this)|@void}}
  {{registerJs}}jQuery('img.lazyload').lazyload();{{/registerJs}}
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
      {{$imageUrl = ''}}
      {{$imageClass = ''}}
      {{if $battle->battleImageResult}}
        {{$imageUrl = $battle->battleImageResult->url}}
      {{elseif $battle->map}}
        {{\app\assets\MapImageAsset::register($this)|@void}}
        {{$mapFile = 'daytime/'|cat:$battle->map->key:'.jpg'}}
        {{$imageUrl = $app->assetManager->getAssetUrl(
            $app->assetManager->getBundle('app\assets\MapImageAsset'),
            $mapFile
          )}}
        {{if $battle->is_win}}
          {{$imageClass = 'image-alt'}}
        {{else}}
          {{$imageClass = 'image-alt-monochrome'}}
        {{/if}}
        {{registerCss}}
          .image-alt {
            -webkit-filter: blur(2px);
            filter: blur(2px);
          }

          .image-alt-monochrome {
            -webkit-filter: blur(2px) grayscale(100%);
            filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/></filter></svg>#grayscale");
            filter: blur(2px) grayscale(100%);
            filter: blur(2px) gray;
          }
        {{/registerCss}}
      {{/if}}
      <a href="{{url route="show/battle" screen_name=$battle->user->screen_name battle=$battle->id}}">
        <img src="{{$placeholder|escape}}" class="lazyload auto-tooltip {{$imageClass|escape}}" data-original="{{$imageUrl|escape}}" title="{{$description|escape}}">
      </a>
    </div>
    <div class="battle-data row">
      <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 omit">
        <a href="{{url route="show/user" screen_name=$battle->user->screen_name}}">{{$battle->user->name|escape}}</a>
      </div>
      <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 time omit">
        {{if $battle->end_at}}
          {{$t = $battle->end_at|date_format:'%Y-%m-%d %H:%M %Z'}}
          <a href="{{url route="show/battle" screen_name=$battle->user->screen_name battle=$battle->id}}" title="{{$t|escape}}" class="auto-tooltip">
            {{$battle->end_at|relative_time:'short'|escape}}
          </a>
        {{/if}}
      </div>
    </div>
  </div>
{{/strip}}
