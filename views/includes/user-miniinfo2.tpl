{{strip}}
  {{\app\assets\UserMiniinfoAsset::register($this)|@void}}
  {{$stat = $user->userStat}}
  <div id="user-miniinfo" itemscope itemtype="http://schema.org/Person" itemprop="author">
    <div id="user-miniinfo-box">
      <h2>
        <a href="{{url route="show-user/profile" screen_name=$user->screen_name}}">
          <span class="miniinfo-user-icon">
            {{if $user->userIcon}}
              <img src="{{$user->userIcon->url|escape}}" width="48" height="48">
            {{else}}
              {{JdenticonWidget hash=$user->identiconHash class="identicon" size="48" schema="image"}}
            {{/if}}
          </span>
          <span itemprop="name" class="miniinfo-user-name">
            {{$user->name|escape}}
          </span>
        </a>
      </h2>
      {{if $stat}}
        <div class="row">
          <div class="col-xs-4">
            <div class="user-label">
              {{'Battles'|translate:'app'|escape}}
            </div>
            <div class="user-number">
              <a href="{{url route="show-v2/user" screen_name=$user->screen_name}}">
                {{$user->getBattle2s()->count()|number_format|escape}}
              </a>
            </div>
          </div>
        </div>
      {{/if}}
      <div class="miniinfo-databox">
        {{if $user->nnid != ''}}
          <div>
            NNID:&#32;
            <a href="https://miiverse.nintendo.net/users/{{$user->nnid|escape:url}}" rel="nofollow" target="_blank">
              {{$user->nnid|escape}}
            </a>
          </div>
        {{/if}}
        {{if $user->sw_friend_code != ''}}
          <div>
            {{'Friend Code'|translate:'app'|escape}}:&#32;
            <span style="white-space:nowrap">
              SW-
              {{$user->sw_friend_code|substr:0:4|escape}}-
              {{$user->sw_friend_code|substr:4:4|escape}}-
              {{$user->sw_friend_code|substr:8:4|escape}}
            </span>
          </div>
        {{/if}}
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
        {{if $user->ikanakama2 != ''}}
          <div>
            <a href="https://ikanakama.ink/users/{{$user->ikanakama2|escape:url}}" rel="nofollow" target="_blank">
              {{'Ika-Nakama 2'|translate:'app'|escape}}
            </a>
          </div>
        {{/if}}
      </div>
    </div>
  </div>
{{/strip}}
