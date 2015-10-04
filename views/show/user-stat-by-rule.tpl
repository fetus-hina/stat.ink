{{strip}}
  {{set layout="main.tpl"}}
  {{\app\assets\FlotPieAsset::register($this)|@void}}
  <div class="container">
    <h1>
      {{$name = '{0}-san'|translate:'app':$user->name}}
      {{$title = "{0}'s Battle Stat (by Rule)"|translate:'app':$name}}
      {{$title|escape}}
      {{set title="{{$app->name}} | {{$title}}"}}
    </h1>
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
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3" style="padding:15px">
        <div style="border:1px solid #ccc;border-radius:5px;padding:15px">
          <h2 style="margin-top:0;margin-bottom:10px">
            <a href="{{url route="show/user" screen_name=$user->screen_name}}">
              {{'{0}-san'|translate:'app':$user->name|escape}}
            </a>
          </h2>
          <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              {{$stat = $user->simpleStatics}}
              <div class="user-label">
                {{'Battles'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                <a href="{{url route="show/user" screen_name=$user->screen_name}}">
                  {{$stat->totalBattleCount|number_format|escape}}
                </a>
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <div class="user-label">
                {{'WP'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                {{if $stat->totalWinRate === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$stat->totalWinRate|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <div class="user-label">
                {{'24H WP'|translate:'app'|escape}}
              </div>
              <div class="user-number">
                {{if $stat->oneDayWinRate === null}}
                  {{'N/A'|translate:'app'|escape}}
                {{else}}
                  {{$stat->oneDayWinRate|string_format:'%.1f%%'|escape}}
                {{/if}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
{{registerJs}}window.statByRule(){{/registerJs}}
{{registerCss}}.pie-flot-container{height:200px}{{/registerCss}}
