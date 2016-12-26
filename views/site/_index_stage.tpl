{{strip}}
  <h3>
    {{$rule->name|translate:'app-rule'|escape}}
  </h3>
  <ul class="battles maps">
    {{foreach $stages as $_}}
      <li>
        <div class="thumbnail thumbnail-{{$rule->key|escape}}">
          {{$mapFile = 'daytime/'|cat:$_->map->key:'.jpg'}}
          <img src="{{$app->assetManager->getAssetUrl(
              $app->assetManager->getBundle('app\assets\MapImageAsset'),
              $mapFile
            )}}">
          <div class="battle-data">
            {{$_->map->name|translate:'app-map'|escape}}
          </div>
          <div class="trends">
            <p>
              {{'Trends'|translate:'app'|escape}}:
            </p>
            <ul>
              {{foreach $_->weaponTrends as $_trend}}
                <li>
                  <a href="{{url route='/entire/weapon' weapon=$_trend->weapon->key rule=$rule->key}}">
                    <img src="{{$_trend->weapon->key|bukiicon|escape}}" title="{{$_trend->weapon->name|translate:'app-weapon'|escape}}" class="auto-tooltip">
                  </a>
                </li>
              {{/foreach}}
            </ul>
          </div>
        </div>
      </li>
    {{/foreach}}
  </ul>
{{/strip}}
{{registerCss}}
{{/registerCss}}
