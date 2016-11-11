{{strip}}
  {{\app\assets\JqueryLazyloadAsset::register($this)|@void}}
  {{registerJs}}jQuery('img.lazyload').lazyload();{{/registerJs}}
  <div class="thumbnail thumbnail-{{$battle->rule->key|default:'unknown'|escape}}">
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
    <div class="caption">
      <div class="caption-line">
        <a href="{{url route="show/user" screen_name=$battle->user->screen_name}}">
          <span class="thumblist-user-icon">
            {{if $battle->user->userIcon}}
              <img src="{{$battle->user->userIcon->url|escape}}" width="46" height="46">
            {{else}}
              {{JdenticonWidget hash=$battle->user->identiconHash class="identicon" size="48"}}
            {{/if}}
          </span>
          <span class="thumblist-user-name">
            {{$battle->user->name|escape}}
          </span>
        </a>
      </div>
      {{if $battle->end_at}}
        <a href="{{url route="show/battle" screen_name=$battle->user->screen_name battle=$battle->id}}" title="{{$battle->end_at|as_datetime:'medium':'short'|escape}}" class="auto-tooltip thumblist-time">
          {{$battle->end_at|active_reltime:'short'}}
        </a>
      {{/if}}
    </div>
  </div>
{{/strip}}
