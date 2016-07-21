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
        {{$result = 'Won'|translate:'app'}}
      {{else}}
        {{$result = 'Lost'|translate:'app'}}
      {{/if}}
      {{$description = "%s / %s / %s"|sprintf:$rule:$map:$result}}
      {{$imageUrl = ''}}
      {{$defaultUrl = ''}}
      {{if $battle->map && $battle->is_win !== null}}
        {{\app\assets\MapImageAsset::register($this)|@void}}
        {{if $battle->is_win}}
          {{$mapFile = 'daytime-blur/'|cat:$battle->map->key:'.jpg'}}
        {{else}}
          {{$mapFile = 'gray-blur/'|cat:$battle->map->key:'.jpg'}}
        {{/if}}
        {{$defaultUrl = $app->assetManager->getAssetUrl(
            $app->assetManager->getBundle('app\assets\MapImageAsset'),
            $mapFile
          )}}
      {{/if}}
      {{if $battle->battleImageResult}}
        {{$imageUrl = $battle->battleImageResult->url}}
      {{/if}}
      <a href="{{url route="show/battle" screen_name=$battle->user->screen_name battle=$battle->id}}">
        <img src="{{$defaultUrl|default:$placeholder|escape}}" class="lazyload auto-tooltip" data-original="{{$imageUrl|escape}}" title="{{$description|escape}}">
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
            {{$battle->end_at|active_reltime:'short'}}
          </a>
        {{/if}}
      </div>
    </div>
  </div>
{{/strip}}
