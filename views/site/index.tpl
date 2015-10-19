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
      {{/if}} | <a href="{{url route="site/start"}}">{{"What's this?"|translate:'app'|escape}}</a> |
      &#32;<a href="{{url route="entire/kd-win"}}">{{"Stat: K/D vs WP"|translate:'app'|escape}}</a>
    </p>
    <p>
      2015-10-20 04:15 JST: stat.ink のサーバを移転しました。IkaLog が投稿しようとしているにもかかわらず stat.ink への投稿に失敗する場合は、IkaLog を再起動してみてください。
    </p>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

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
