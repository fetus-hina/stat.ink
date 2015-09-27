{{strip}}
  {{set layout="main.tpl"}}
  {{use class="app\models\Battle"}}
  <div class="container">
    <h1>
      {{$app->name|escape}}
    </h1>
    {{$battles = Battle::find()->with('user')->limit(50)->all()}}
    <ul class="battles">
      {{foreach $battles as $battle}}
        <li>
          <div class="battle-image">
            <a href="{{url route="show/battle" screen_name=$battle->user->screen_name battle=$battle->id}}">
              {{$image = null}}
              {{if $battle->battleImageJudge}}
                {{$image = $battle->battleImageJudge}}
              {{elseif $battle->battleImageResult}}
                {{$image = $battle->battleImageResult}}
              {{/if}}
              {{if $image}}
                <img src="{{$image->url|escape}}">
              {{else}}
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQI12NgYAAAAAMAASDVlMcAAAAASUVORK5CYII=" class="no-image">
              {{/if}}
            </a>
          </div>
          <div class="battle-data">
            <a href="{{url route="show/user" screen_name=$battle->user->screen_name}}">{{$battle->user->name|escape}}</a>
          </div>
        </li>
      {{/foreach}}
    </ul>
  </div>
{{/strip}}
