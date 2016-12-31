{{strip}}
  <h3>
    {{$rule->name|translate:'app-rule'|escape}}
  </h3>
  <ul class="battles maps">
    {{foreach $stages as $_}}
      <li>
        <div class="thumbnail thumbnail-{{$rule->key|escape}}">
          <a href="{{url route="/stage/map-detail" rule=$rule->key map=$_->map->key}}">
            {{$mapFile = 'daytime/'|cat:$_->map->key:'.jpg'}}
            <img src="{{$app->assetManager->getAssetUrl(
                $app->assetManager->getBundle('app\assets\MapImageAsset'),
                $mapFile
              )}}">
          </a>
          <div class="battle-data">
            <a href="{{url route="/stage/map-detail" rule=$rule->key map=$_->map->key}}">
              {{$_->map->name|translate:'app-map'|escape}}
            </a>
          </div>
          <div class="trends">
            <p>
              <a href="{{url route="/stage/map-detail" rule=$rule->key map=$_->map->key}}#weapons">
                {{'Trends'|translate:'app'|escape}}:
              </a>
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
