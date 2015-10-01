{{strip}}
  {{set layout="main.tpl"}}
  {{use class="app\models\Battle"}}
  <div class="container">
    <h1>
      {{$app->name|escape}}
    </h1>
    <p>
      {{'Enjoy Splatoon!'|translate:'app'|escape}}<br>
      {{if $app->user->isGuest}}
        <a href="{{url route="user/register"}}">{{'Join us'|translate:'app'|escape}}</a>
      {{else}}
        {{$ident = $app->user->identity}}
        <a href="{{url route="show/user" screen_name=$ident->screen_name}}">{{'Your Battles'|translate:'app'|escape}}</a>
      {{/if}} | <a href="{{url route="site/start"}}">{{"What's this?"|translate:'app'|escape}}</a>
    </p>
    {{$battles = Battle::find()->with('user')->limit(100)->all()}}
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
