{{strip}}
  {{$stat = $user->userStat}}
  <div id="user-miniinfo" style="margin-bottom:15px">
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
              {{'Win %'|translate:'app'|escape}}
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
              {{'24H Win %'|translate:'app'|escape}}
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
              {{'Avg Killed'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{if $stat->total_kd_battle_count > 0}}
                {{$p = ['number' => $stat->total_kill, 'battle' => $stat->total_kd_battle_count]}}
                {{$s = '{number} killed in {battle, plural, =1{1 battle} other{# battles}}'|translate:'app':$p}}
                <span class="auto-tooltip" title="{{$s|escape}}">
                  {{($stat->total_kill/$stat->total_kd_battle_count)|string_format:'%.2f'|escape}}
                </span>
              {{else}}
                {{'N/A'|translate:'app'|escape}}
              {{/if}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Avg Dead'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              {{if $stat->total_kd_battle_count > 0}}
                {{$p = ['number' => $stat->total_death, 'battle' => $stat->total_kd_battle_count]}}
                {{$s = '{number} dead in {battle, plural, =1{1 battle} other{# battles}}'|translate:'app':$p}}
                <span class="auto-tooltip" title="{{$s|escape}}">
                  {{($stat->total_death/$stat->total_kd_battle_count)|string_format:'%.2f'|escape}}
                </span>
              {{else}}
                {{'N/A'|translate:'app'|escape}}
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
        <hr>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="user-label">
              {{'Turf War'|translate:'app-rule'|escape}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Battles'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              <a href="{{url route="show/user" screen_name=$user->screen_name filter=["rule" => "nawabari"]}}">
                {{$stat->nawabari_count|number_format|escape}}
              </a>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Win %'|translate:'app'|escape}}
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
        <hr>
        {{* ガチ *}}
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="user-label">
              {{'Ranked Battle'|translate:'app-rule'|escape}}
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Battles'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              <a href="{{url route="show/user" screen_name=$user->screen_name filter=["rule" => "@gachi"]}}">
                {{$stat->gachi_count|number_format|escape}}
              </a>
            </div>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <div class="user-label">
              {{'Win %'|translate:'app'|escape}}
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
        <hr>
        <p style="margin:10px 0 0">
          <a href="{{url route="show/user-stat-nawabari" screen_name=$user->screen_name}}">
            <span class="fa fa-pie-chart left"></span>
            {{'Stats (Turf War)'|translate:'app'|escape}}
          </a><br>
          <a href="{{url route="show/user-stat-gachi" screen_name=$user->screen_name}}">
            <span class="fa fa-pie-chart left"></span>
            {{'Stats (Ranked Battle)'|translate:'app'|escape}}
          </a><br>
          <a href="{{url route="show/user-stat-by-rule" screen_name=$user->screen_name}}">
            <span class="fa fa-pie-chart left"></span>
            {{'Stats (by Mode)'|translate:'app'|escape}}
          </a><br>
          <a href="{{url route="show/user-stat-by-map" screen_name=$user->screen_name}}">
            <span class="fa fa-pie-chart left"></span>
            {{'Stats (by Map)'|translate:'app'|escape}}
          </a><br>
          <a href="{{url route="show/user-stat-by-map-rule" screen_name=$user->screen_name}}">
            <span class="fa fa-pie-chart left"></span>
            {{'Stats (by Mode and Map)'|translate:'app'|escape}}
          </a><br>
          <a href="{{url route="show/user-stat-cause-of-death" screen_name=$user->screen_name}}">
            <span class="fa fa-pie-chart left"></span>
            {{'Stats (Cause of Death)'|translate:'app'|escape}}
          </a>
        </p>
      {{/if}}
      {{if $user->mainWeapon}}
        <div style="margin:15px 0 0">
          {{'Favorite Weapon'|translate:'app'|escape}}:&#32;
          {{$user->mainWeapon->name|translate:'app-weapon'|escape}}<br>
          <a href="{{url route="show/user-stat-weapon" screen_name=$user->screen_name}}">
            {{'List'|translate:'app'|escape}}
          </a>
        </div>
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
              <span class="fa fa-twitter left"></span>{{$user->twitter|escape}}
            </a>
          </div>
        {{/if}}
        {{if $user->ikanakama != ''}}
          <div>
            <a href="http://ikazok.net/users/{{$user->ikanakama|escape:url}}" rel="nofollow" target="_blank">
              {{'Ika-Nakama'|translate:'app'|escape}}
            </a>
          </div>
        {{/if}}
      </div>
    </div>
  </div>
{{/strip}}
