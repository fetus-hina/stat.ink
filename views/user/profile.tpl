{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Profile and Settings'|translate:'app'}}"}}
  {{use class="yii\helpers\Html"}}
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
        <h1>
          {{'Profile and Settings'|translate:'app'|escape}}

          <a href="{{url route="user/edit-profile"}}" class="btn btn-primary" style="margin-left:30px">
            <span class="fa fa-pencil left"></span>
            {{'Update'|translate:'app'|escape}}
          </a>
        </h1>

        <table class="table table-striped">
          <tbody>
            <tr>
              <th>{{'Internal ID'|translate:'app'|escape}}</th>
              <td>#{{$user->id|escape}}</td>
            </tr>
            <tr>
              <th>{{'Icon'|translate:'app'|escape}}</th>
              <td>
                {{registerCss}}
                  .profile-icon-container {
                    display: flex;
                    flex-direction: row;
                    flex-wrap: wrap;
                    align-items: baseline;
                  }

                  .profile-icon {
                    align-self: center;
                    display:inline-block;
                    border:1px solid #ccc;
                    border-radius:4px;
                    background-color:#fff;
                    margin-right:1ex;
                    line-height:1px;
                  }

                  .profile-icon-text {
                    margin-right: 1ex;
                  }
                {{/registerCss}}
                <div class="profile-icon-container">
                  {{if $user->userIcon}}
                    <span class="profile-icon">
                      <img src="{{$user->userIcon->url|escape}}" width="48" height="48">
                    </span>
                    <span class="profile-icon-text">
                    </span>
                  {{else}}
                    <span class="profile-icon">
                      {{JdenticonWidget hash=$user->identiconHash class="identicon" size="48"}}
                    </span>
                    <span class="profile-icon-text">
                      {{'Auto (Identicon)'|translate:'app'|escape}}
                    </span>
                  {{/if}}
                  <a href="{{url route="user/edit-icon"}}" class="btn btn-default">
                    <span class="fa fa-picture-o left"></span>
                    {{'Change Icon'|translate:'app'|escape}}
                  </a>
                </div>
              </td>
            </tr>
            <tr>
              <th>{{'Screen Name'|translate:'app'|escape}}</th>
              <td><code>{{$user->screen_name|escape}}</code></td>
            </tr>
            <tr>
              <th>{{'Password'|translate:'app'|escape}}</th>
              <td>
                <code>**********</code>&#32;
                <a href="{{url route="user/edit-password"}}" class="btn btn-default">
                  <span class="fa fa-repeat left"></span>
                  {{'Change Password'|translate:'app'|escape}}
                </a>
              </td>
            </tr>
            <tr>
              <th>{{'API Token'|translate:'app'|escape}}</th>
              <td>
                <button class="btn btn-default auto-tooltip" title="{{'Show your API Token'|translate:'app'|escape}}" id="apikey-button">
                  <span class="fa fa-eye"></span>
                </button>
                <code id="apikey" style="display:none">{{$user->api_key|escape}}</code>
              </td>
            </tr>
            <tr>
              <th>{{'User Name'|translate:'app'|escape}}</th>
              <td>{{$user->name|escape}}</td>
            </tr>
            <tr>
              <th>{{'Black out other players'|translate:'app'|escape}}</th>
              <td>
                <p>
                  {{use class="app\models\User"}}
                  {{if $user->blackout == User::BLACKOUT_NOT_BLACKOUT}}
                    {{'No black out'|translate:'app'|escape}}
                  {{elseif $user->blackout == User::BLACKOUT_NOT_PRIVATE}}
                    {{'Black out except private battle'|translate:'app'|escape}}
                  {{elseif $user->blackout == User::BLACKOUT_NOT_FRIEND}}
                    {{'Black out except private battle and teammate on squad battle (tri or quad)'|translate:'app'|escape}}
                  {{elseif $user->blackout == User::BLACKOUT_ALWAYS}}
                    {{'Black out other players'|translate:'app'|escape}}
                  {{else}}
                    ({{$user->blackout|escape}})
                  {{/if}}
                </p>
                <div>
                  {{include file="_blackout-hint.tpl"}}
                  {{registerJs}}
                    updateBlackOutHint('{{$user->blackout|escape:javascript}}');
                  {{/registerJs}}
                </div>
              </td>
            </tr>
            <tr>
              <th>{{'Nintendo Network ID'|translate:'app'|escape}}</th>
              <td>
                {{if $user->nnid != ''}}
                  <a href="https://miiverse.nintendo.net/users/{{$user->nnid|escape:url}}">
                    {{$user->nnid|escape}}
                  </a>
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'Twitter @name'|translate:'app'|escape}}</th>
              <td>
                {{if $user->twitter != ''}}
                  <a href="https://twitter.com/{{$user->twitter|escape:url}}">
                    <span class="fa fa-twitter left"></span>@{{$user->twitter|escape}}
                  </a>
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'IKANAKAMA User ID'|translate:'app'|escape}}</th>
              <td>
                {{if $user->ikanakama != ''}}
                  <a href="http://ikazok.net/users/{{$user->ikanakama|escape:url}}">
                    #{{$user->ikanakama|escape}}
                  </a>
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
            <tr>
              <th>{{'Capture Environment'|translate:'app'|escape}}</th>
              <td>
                {{if $user->env}}
                  {{$user->env->text|escape|nl2br}}
                {{else}}
                  -
                {{/if}}
              </td>
            </tr>
          </tbody>
        </table>
        <h2>
          {{'Log in with other services'|translate:'app'|escape}}
        </h2>
        <table class="table table-striped">
          <tbody>
            <tr>
              <th>
                <span class="fa fa-twitter left"></span>
                Twitter
              </th>
              <td>
                {{if $app->params['twitter']['read_enabled']}}
                  {{$_tw = $user->loginWithTwitter}}
                  {{if $_tw}}
{{registerJs}}{{/strip}}{{literal}}
(function() {
  if (window.__twitterIntentHandler) return;
  var intentRegex = /twitter\.com\/intent\/(\w+)/,
      windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes',
      width = 550,
      height = 420,
      winHeight = screen.height,
      winWidth = screen.width;

  function handleIntent(e) {
    e = e || window.event;
    var target = e.target || e.srcElement,
        m, left, top;

    while (target && target.nodeName.toLowerCase() !== 'a') {
      target = target.parentNode;
    }

    if (target && target.nodeName.toLowerCase() === 'a' && target.href) {
      m = target.href.match(intentRegex);
      if (m) {
        left = Math.round((winWidth / 2) - (width / 2));
        top = 0;

        if (winHeight > height) {
          top = Math.round((winHeight / 2) - (height / 2));
        }

        window.open(target.href, 'intent', windowOptions + ',width=' + width +
                                           ',height=' + height + ',left=' + left + ',top=' + top);
        e.returnValue = false;
        e.preventDefault && e.preventDefault();
      }
    }
  }

  if (document.addEventListener) {
    document.addEventListener('click', handleIntent, false);
  } else if (document.attachEvent) {
    document.attachEvent('onclick', handleIntent);
  }
  window.__twitterIntentHandler = true;
}());
{{/literal}}{{strip}}{{/registerJs}}
                    <a href="https://twitter.com/intent/user?user_id={{$_tw->twitter_id|escape:url}}">
                      @{{$_tw->screen_name|escape}}
                    </a>
                    &#32;
                    ({{$_tw->name|escape}})
                    &#32;
                    <a href="{{url route="user/update-login-with-twitter"}}" class="btn btn-primary">
                      <span class="fa fa-link left"></span>
                      {{'Another account'|translate:'app'|escape}}
                    </a>
                    &#32;
                    <a href="{{url route="user/clear-login-with-twitter"}}" class="btn btn-danger">
                      <span class="fa fa-unlink left"></span>
                      {{'Unlink account'|translate:'app'|escape}}
                    </a>
                  {{else}}
                    {{'Disabled'|translate:'app'|escape}}&#32;
                    <a href="{{url route="user/update-login-with-twitter"}}" class="btn btn-primary">
                      <span class="fa fa-link left"></span>
                      {{'Integrate'|translate:'app'|escape}}
                    </a>
                  {{/if}}
                {{else}}
                  {{'Not configured.'|translate:'app'|escape}}
                {{/if}}
              </td>
            </tr>
          </tbody>
        </table>
        <h2>
          {{'Slack Integration'|translate:'app'|escape}}

          <a href="{{url route="user/slack-add"}}" class="btn btn-primary" style="margin-left:30px">
            <span class="fa fa-plus"></span>
          </a>
        </h2>

        <table class="table table-striped">
          <thead>
            <tr>
              <th>{{'Enabled'|translate:'app'|escape}}</th>
              <th>{{'User Name'|translate:'app'|escape}}</th>
              <th>{{'Icon'|translate:'app'|escape}}</th>
              <th>{{'Channel'|translate:'app'|escape}}</th>
              <th>{{'Language'|translate:'app'|escape}}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {{$_slacks = $user->getSlacks()->with('language')->all()}}
            {{foreach $_slacks as $_slack}}
              <tr>
                <td>
                  {{\app\assets\SlackAsset::register($this)|@void}}
                  {{Html::checkbox(
                      "slack-{{$_slack->id}}",
                      !$_slack->suspended,
                      [
                        "class" => [ "slack-toggle-enable" ],
                        "data" => [
                          "toggle" => "toggle",
                          "on" => "Enabled"|translate:'app',
                          "off" => "Disabled"|translate:'app',
                          "id" => $_slack->id
                        ],
                        "disabled" => true
                      ]
                    )}}
                </td>
                <td>
                  {{if $_slack->username == ''}}
                    {{'(default)'|translate:'app'|escape}}
                  {{else}}
                    {{$_slack->username|escape}}
                  {{/if}}
                </td>
                <td>
                  {{if $_slack->icon == ''}}
                    {{'(default)'|translate:'app'|escape}}
                  {{elseif $_slack->icon|substr:0:4|strtolower == 'http' || $_slack->icon|substr:0:2 == '//'}}
                    {{Html::img($_slack->icon, ['class' => 'emoji emoji-url'])}}
                  {{elseif preg_match('/^:[a-zA-Z0-9+._-]+:$/', $_slack->icon)}}
                    {{$emojiAsset = \app\assets\EmojifyResourceAsset::register($this)}}
                    {{$emojiFileName = $_slack->icon|trim:':'|cat:'.png'}}
                    {{Html::img(
                        $app->assetmanager->getAssetUrl($emojiAsset, $emojiFileName),
                        [
                          'style' => [
                            'height' => '2em',
                            'width' => 'auto'
                          ]
                        ]
                      )}}
                    {{$_slack->icon|escape}}
                  {{/if}}
                </td>
                <td>
                  {{if $_slack->channel == ''}}
                    {{'(default)'|translate:'app'|escape}}
                  {{else}}
                    {{$_slack->channel|escape}}
                  {{/if}}
                </td>
                <td>
                  {{$_slack->language->name|escape}}
                </td>
                <td>
                  <button disabled class="slack-test btn btn-info btn-sm" data-id="{{$_slack->id|escape}}">
                    {{'Test'|translate:'app'|escape}}
                  </button>
                  &#32;
                  <button disabled class="slack-del btn btn-danger btn-sm" data-id="{{$_slack->id|escape}}">
                    {{'Delete'|translate:'app'|escape}}
                  </button>
                </td>
              </tr>
            {{foreachelse}}
              <tr>
                <td colspan="6">
                  {{'There are no data.'|translate:'app'|escape}}
                </td>
              </tr>
            {{/foreach}}
          </tbody>
        </table>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <h2>{{'Export'|translate:'app'|escape}}</h2>
        <p>
          <a href="{{url route="/user/download" type="ikalog-csv"}}" class="btn btn-default btn-block">
            <span class="fa fa-file-excel-o left"></span>
            {{'CSV (IkaLog compat.)'|translate:'app'|escape}}
          </a>
          <a href="{{url route="/user/download" type="ikalog-json"}}" class="btn btn-default btn-block">
            <span class="fa fa-file-code-o left"></span>
            {{'JSON (IkaLog compat.)'|translate:'app'|escape}}
          </a>
          {{if $user->isUserJsonReady}}
            <a href="{{url route="/user/download" type="user-json"}}" class="btn btn-default btn-block">
              <span class="fa fa-file-code-o left"></span>
              {{'JSON (stat.ink format, gzipped)'|translate:'app'|escape}}
            </a>
          {{else}}
            <button class="btn btn-default btn-block" disabled>
              <span class="fa fa-file-code-o left"></span>
              {{'JSON (stat.ink format, gzipped)'|translate:'app'|escape}}
            </button>
          {{/if}}
        </p>
      </div>
    </div>
  </div>
{{/strip}}
{{registerCss}}
  tbody th { width: 10em; }
{{/registerCss}}
{{registerJs}}{{strip}}
  (function($){
    "use strict";
    $('#apikey-button').click(function () {
      $(this).hide();
      $('#apikey').show();
    });
  })(jQuery);
{{/strip}}{{/registerJs}}
