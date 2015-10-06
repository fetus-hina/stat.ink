{{strip}}
  <div id="user-miniinfo">
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
      <p style="margin:15px 0 0">
        <a href="{{url route="show/user-stat-by-rule" screen_name=$user->screen_name}}">
          <span class="fa fa-pie-chart"></span>&#32;
          {{'Stat (by Rule)'|translate:'app'|escape}}
        </a><br>
        <a href="{{url route="show/user-stat-by-map" screen_name=$user->screen_name}}">
          <span class="fa fa-pie-chart"></span>&#32;
          {{'Stat (by Map)'|translate:'app'|escape}}
        </a>
      </p>
      <div style="margin:15px 0 0">
        <div>
          NNID:&#32;
          {{if $user->nnid == ''}}
            ?
          {{else}}
            <a href="https://miiverse.nintendo.net/users/{{$user->nnid|escape:url}}" rel="nofollow" target="_blank">
              {{$user->nnid|escape}}
            </a>
          {{/if}}
        </div>
        {{if $user->twitter != ''}}
          <div>
            <a href="https://twitter.com/{{$user->twitter|escape:url}}" rel="nofollow" target="_blank">
              <span class="fa fa-twitter"></span> {{$user->twitter|escape}}
            </a>
          </div>
        {{/if}}
        {{if $user->ikanakama != ''}}
          <div>
            <a href="http://ikazok.net/users/{{$user->ikanakama|escape:url}}" rel="nofollow" target="_blank">
              イカナカマ
            </a>
          </div>
        {{/if}}
      </div>
    </div>
  </div>
{{/strip}}
