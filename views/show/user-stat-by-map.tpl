{{strip}}
  {{set layout="main.tpl"}}
  {{\app\assets\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$name = '{0}-san'|translate:'app':$user->name}}
      {{$title = "{0}'s Battle Stat (by Map)"|translate:'app':$name}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>

    <div id="sns">
      {{\app\assets\TwitterWidgetAsset::register($this)|@void}}
      <a class="twitter-share-button" href="https://twitter.com/intent/tweet" data-count="none"><span class="fa fa-twitter"></span></a>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
        <div id="loading">
          <h2>
            <span class="fa fa-spinner fa-pulse"></span>&#32;
            {{'Loading...'|translate:'app'|escape}}
          </h2>
          <p>
            {{'Just a moment, please.'|translate:'app'|escape}}
          </p>
        </div>
        <div id="stat" data-screen-name="{{$user->screen_name|escape}}" data-no-data="{{'No Data'|translate:'app'|escape}}"></div>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
        {{include file="@app/views/includes/user-miniinfo.tpl" user=$user}}
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 pull-right" style="margin-top:15px">
        {{include file="@app/views/includes/ad.tpl"}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerJs}}window.statByMap(){{/registerJs}}
{{registerCss}}.pie-flot-container{height:200px}{{/registerCss}}
