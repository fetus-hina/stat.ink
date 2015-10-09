{{strip}}
  {{$stat = $user->userStat}}
  <div id="user-miniinfo">
    <div style="border:1px solid #ccc;border-radius:5px;padding:15px">
      <h2 style="margin-top:0;margin-bottom:10px">
        <a href="{{url route="show/user" screen_name=$user->screen_name}}">
          {{'{0}-san'|translate:'app':$user->name|escape}}
        </a>
      </h2>
      {{if $stat}}
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Battles'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              <a href="{{url route="show/user" screen_name=$user->screen_name}}">
                {{$stat->battle_count|number_format|escape}}
              </a>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'WP'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{if $stat->wp === null}}
                {{'N/A'|translate:'app'|escape}}
              {{else}}
                {{$stat->wp|string_format:'%.1f%%'|escape}}
              {{/if}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'24H WP'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{if $stat->wp_short === null}}
                {{'N/A'|translate:'app'|escape}}
              {{else}}
                {{$stat->wp_short|string_format:'%.1f%%'|escape}}
              {{/if}}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Killed'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{$stat->total_kill|number_format|escape}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Dead'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{$stat->total_death|number_format|escape}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              <span class="auto-tooltip" title="{{'Kill Ratio'|translate:'app'|escape}}">
                {{'KR'|translate:'app'|escape}}
              </span>
            </div>
            <div class="user-number">
              {{if $stat->total_kill == 0 && $stat->total_death == 0}}
                -
              {{elseif $stat->total_death == 0}}
                ∞
              {{else}}
                {{($stat->total_kill/$stat->total_death)|string_format:'%.2f'|escape}}
              {{/if}}
            </div>
          </div>
        </div>

        {{* ナワバリ *}}
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Turf War'|translate:'app-rule'|escape}}
            </div>
            <div class="user-number">
              <a href="{{url route="show/user" screen_name=$user->screen_name filter=["rule" => "nawabari"]}}">
                {{$stat->nawabari_count|number_format|escape}}
              </a>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'WP'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{if $stat->nawabari_wp === null}}
                {{'N/A'|translate:'app'|escape}}
              {{else}}
                {{$stat->nawabari_wp|string_format:'%.1f%%'|escape}}
              {{/if}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              <span class="auto-tooltip" title="{{'Kill Ratio'|translate:'app'|escape}}">
                {{'KR'|translate:'app'|escape}}
              </span>
            </div>
            <div class="user-number">
              {{if $stat->nawabari_kill == 0 && $stat->nawabari_death == 0}}
                -
              {{elseif $stat->nawabari_death == 0}}
                ∞
              {{else}}
                {{($stat->nawabari_kill/$stat->nawabari_death)|string_format:'%.2f'|escape}}
              {{/if}}
            </div>
          </div>
        </div>

        {{* ナワバリ *}}
        <div class="row">
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Ranked Battle'|translate:'app-rule'|escape}}
            </div>
            <div class="user-number">
              <a href="{{url route="show/user" screen_name=$user->screen_name filter=["rule" => "@gachi"]}}">
                {{$stat->gachi_count|number_format|escape}}
              </a>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'WP'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{if $stat->gachi_wp === null}}
                {{'N/A'|translate:'app'|escape}}
              {{else}}
                {{$stat->gachi_wp|string_format:'%.1f%%'|escape}}
              {{/if}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              <span class="auto-tooltip" title="{{'Kill Ratio'|translate:'app'|escape}}">
                {{'KR'|translate:'app'|escape}}
              </span>
            </div>
            <div class="user-number">
              {{if $stat->gachi_kill == 0 && $stat->gachi_death == 0}}
                -
              {{elseif $stat->gachi_death == 0}}
                ∞
              {{else}}
                {{($stat->gachi_kill/$stat->gachi_death)|string_format:'%.2f'|escape}}
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
      {{/if}}
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
{{registerCss}}.user-label{text-overflow:ellipsis;white-space:nowrap;overflow:hidden}{{/registerCss}}
