{{strip}}
  {{\app\assets\BattleThumbListAsset::register($this)|@void}}
  {{$imagePlaceholder = $app->assetManager->getAssetUrl(
      $app->assetManager->getBundle('app\assets\AppAsset'),
      'no-image.png'
    )}}
  <ul class="battles">
    {{foreach $battles as $battle}}
      <li>
        {{include file="@app/views/includes/battle_thumb.tpl"
            placeholder=$imagePlaceholder
            battle=$battle
          }}
      </li>
    {{/foreach}}
  </ul>
{{/strip}}
