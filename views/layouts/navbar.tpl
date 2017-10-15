{{strip}}
{{use class="app\models\Timezone"}}
{{use class="app\models\Language"}}
{{\hiqdev\assets\flagiconcss\FlagIconCssAsset::register($this)|@void}}
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        {{\app\assets\PaintballAsset::register($this)|@void}}
        <a itemprop="name" class="navbar-brand paintball" href="/" style="font-size:24px">
          {{$app->name|escape}}
        </a>
        <span class="navbar-brand ip-via-badge">
          {{IpVersionBadgeWidget}}
          {{registerCss}}
            .ip-via-badge {
              position: relative;
              top: -3px;
            }
          {{/registerCss}}
        </span>
      </div>
      <div itemscope itemtype="http://schema.org/SiteNavigationElement" class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            {{$user = $app->user}}
            {{if $user->isGuest}}
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-user left"></span>{{'Guest'|translate:'app'|escape}}&#32;<span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="{{path route="user/login"}}">
                    <span class="fa fa-sign-in fa-fw left"></span>{{'Login'|translate:'app'|escape}}
                  </a>
                </li>
                {{if $app->params['twitter']['read_enabled']}}
                  <li>
                    <a href="{{path route="user/login-with-twitter"}}">
                      └ <span class="fa fa-twitter fa-fw"></span> {{'Log in with Twitter'|translate:'app'|escape}}
                    </a>
                  </li>
                {{/if}}
                <li>
                  <a href="{{path route="user/register"}}">
                    <span class="fa fa-plus fa-fw left"></span>{{'Register'|translate:'app'|escape}}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="javascript:;" id="toggle-color-lock">
                    <span class="fa fa-fw left"></span>{{'Color-Blind Support'|translate:'app'|escape}}
                  </a>
                </li>
                <li>
                  <a href="javascript:;" id="toggle-use-fluid">
                    <span class="fa fa-fw left"></span>{{'Use full width of the screen'|translate:'app'|escape}}
                  </a>
                </li>
              </ul>
            {{else}}
              {{$ident = $user->identity}}
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <img src="{{$ident->userIcon->url|default:$ident->jdenticonPngUrl|escape}}" style="width:1em;height:1em;background-color:#fff">
                &#32;
                {{$ident->name|escape}}&#32;<span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="{{path route="show-user/profile" screen_name=$ident->screen_name}}">
                    <span class="fa fa-fw fa-user"></span>{{'Your Battles'|translate:'app'|escape}}
                  </a>
                </li>
                <li>
                  <a href="{{path route="show/user" screen_name=$ident->screen_name}}">
                    <span class="fa fa-fw"></span>
                    ┣ Splatoon
                  </a>
                </li>
                <li>
                  <a href="{{path route="show-v2/user" screen_name=$ident->screen_name}}">
                    <span class="fa fa-fw"></span>
                    ┗ Splatoon 2
                  </a>
                </li>
                <li>
                  <a href="{{path route="user/profile"}}">
                    <span class="fa fa-fw fa-wrench"></span>{{'Profile and Settings'|translate:'app'|escape}}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="{{path route="user/logout"}}">
                    <span class="fa fa-fw fa-sign-out"></span>{{'Logout'|translate:'app'|escape}}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="javascript:;" id="toggle-color-lock">
                    <span class="fa fa-fw"></span>{{'Color-Blind Support'|translate:'app'|escape}}
                  </a>
                </li>
                <li>
                  <a href="javascript:;" id="toggle-use-fluid">
                    <span class="fa fa-fw"></span>{{'Use full width of the screen'|translate:'app'|escape}}
                  </a>
                </li>
              </ul>
            {{/if}}
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <span class="fa fa-language left"></span>Language&#32;<span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              {{foreach Language::find()->orderBy('name ASC')->all() as $lang}}
                <li>
                  <a href="javascript:;" hreflang="{{$lang->lang|escape}}"" data-lang="{{$lang->lang|escape}}" class="language-change">
                    <span class="fa {{if $app->language === $lang->lang}}fa-check {{/if}}fa-fw left"></span>
                    <span class="flag-icon flag-icon-{{$lang->lang|substr:3:2|strtolower|escape}}"></span>&#32;
                    {{$lang->name|escape}} / {{$lang->name_en|escape}}
                  </a>
                </li>
              {{/foreach}}
              <li class="divider"></li>
              <li>
                <a href="{{url route="site/translate"}}">
                  <span class="fa fa-question-circle left"></span>About Translation
                </a>
              </li>
            </ul>
          </li>
          <li class="dropdown">
            {{$this->render('/layouts/navbar/timezone')}}
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <span class="fa fa-link left"></span>
              {{'Links'|translate:'app'|escape}}&#32;<span class="caret"></span>
            </a>
            {{$_linkIcon = \app\assets\AppLinkAsset::register($this)}}
            <ul class="dropdown-menu">
              <li class="dropdown-submenu">
                {{$_params = ['title' => 'Splatoon 2']}}
                <a href="javascript:;" data-toggle="dropdown">
                  {{'{title} Official Website'|translate:'app':$_params|escape}}
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="https://www.nintendo.co.jp/switch/aab6a/">
                      <span class="flag-icon flag-icon-jp"></span>&#32;
                      {{'Japan'|translate:'app'|escape}}
                    </a>
                  </li>
                  <li>
                    <a href="http://splatoon.nintendo.com/">
                      <span class="flag-icon flag-icon-us"></span>&#32;
                      {{'North America'|translate:'app'|escape}}
                    </a>
                  </li>
                  <li>
                    <a href="https://www.nintendo.co.uk/Games/Nintendo-Switch/Splatoon-2-1173295.html">
                      <span class="flag-icon flag-icon-eu"></span>&#32;
                      {{'Europe'|translate:'app'|escape}}
                    </a>
                  </li>
                </ul>
              </li>
              <li class="dropdown-submenu">
                {{$_params = ['title' => 'Splatoon']}}
                <a href="javascript:;" data-toggle="dropdown">
                  {{'{title} Official Website'|translate:'app':$_params|escape}}
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="http://www.nintendo.co.jp/wiiu/agmj/">
                      <span class="flag-icon flag-icon-jp"></span>&#32;
                      {{'Japan'|translate:'app'|escape}}
                    </a>
                  </li>
                  <li>
                    <a href="http://splatoon.nintendo.com/splatoon/">
                      <span class="flag-icon flag-icon-us"></span>&#32;
                      {{'North America'|translate:'app'|escape}}
                    </a>
                  </li>
                  <li>
                    <a href="https://www.nintendo.co.uk/Games/Wii-U/Splatoon-892510.html">
                      <span class="flag-icon flag-icon-eu"></span>&#32;
                      {{'Europe'|translate:'app'|escape}}
                    </a>
                  </li>
                </ul>
              </li>
              <li class="dropdown-submenu">
                <a href="javascript:;" data-toggle="dropdown">
                  {{registerCss}}
                    .fa-twitter{
                      color:#1da1f2;
                    }
                  {{/registerCss}}
                  <span class="fa fa-fw fa-twitter"></span>
                  {{'Official Twitter'|translate:'app'|escape}}
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="https://twitter.com/splatoonjp">
                      <span class="flag-icon flag-icon-jp"></span>&#32;
                      {{'Japan'|translate:'app'|escape}}
                    </a>
                  </li>
                </ul>
              </li>
              <li class="divider"></li>
              <li>
                <a>{{'Nintendo Switch Online app'|translate:'app'|escape}}</a>
              </li>
              <li>
                <a href="https://play.google.com/store/apps/details?id=com.nintendo.znca">
                  <span class="fa fa-fw">├</span>
                  <span class="fa fa-fw fa-android"></span>Android
                </a>
              </li>
              <li>
                <a href="https://itunes.apple.com/app/nintendo-switch-online/id1234806557">
                  <span class="fa fa-fw">└</span>
                  <span class="fa fa-fw fa-apple"></span>iOS (iPhone/iPad)
                </a>
              </li>
              <li class="divider"></li>
              <li>
                {{if $app->language === 'ja-JP'}}
                  <a href="https://github.com/hasegaw/IkaLog/wiki/ja_WinIkaLog">
                    {{$_linkIcon->ikalog}}&#32;
                    {{'IkaLog'|translate:'app'|escape}}
                    （<span class="fa fa-windows left"></span>
                    <span class="fa fa-apple left"></span>
                    <span class="fa fa-linux"></span> /&#32;
                    日本語, English）
                  </a>
                {{else}}
                  <a href="https://github.com/hasegaw/IkaLog/wiki/en_Home">
                    {{$_linkIcon->ikalog}}&#32;
                    {{'IkaLog'|translate:'app'|escape}}
                    （<span class="fa fa-windows left"></span>
                    <span class="fa fa-apple left"></span>
                    <span class="fa fa-linux"></span> /&#32;
                    English, 日本語）
                  </a>
                {{/if}}
              </li>
              <li>
                <a href="https://hasegaw.github.io/IkaLog/">
                  └&#32;
                  <span class="fa fa-download left"></span>
                  {{'IkaLog Download Page'|translate:'app'|escape}}（<span class="fa fa-windows"></span>）
                </a>
              </li>
              <li>
                <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec">
                  {{$_linkIcon->ikaRecJa}}&#32;
                  {{'IkaRec'|translate:'app'|escape}}（<span class="fa fa-android"></span> / 日本語版 / Splatoon）
                </a>
              </li>
              <li>
                <a href="https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec">
                  └&#32;
                  {{$_linkIcon->ikaRecEn}}&#32;
                   {{'IkaRec'|translate:'app'|escape}} (<span class="fa fa-android"></span> / English version / Splatoon)
                </a>
              </li>
              <li>
                <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec2">
                  {{$_linkIcon->ikaRecJa}}&#32;
                  {{'IkaRec 2'|translate:'app'|escape}} (<span class="fa fa-android"></span> / Splatoon 2）
                </a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://fest.ink/">
                  {{$_linkIcon->festink}}&#32;
                  {{'fest.ink'|translate:'app'|escape}}
                </a>
              </li>
              <li>
                <a href="https://ikadenwa.ink/">
                  {{$_linkIcon->ikadenwa}}&#32;
                  {{'Ika-Denwa'|translate:'app'|escape}}
                </a>
              </li>
              <li>
                <a href="https://ikanakama.ink/">
                  {{$_linkIcon->ikanakama}}&#32;
                  {{'Ika-Nakama 2'|translate:'app'|escape}}
                </a>
              </li>
            </ul>
          </li>
        </ul>
        {{if !$app->user->isGuest}}
          <ul class="nav navbar-nav navbar-right">
            <li style="margin-right:1ex">
              <button id="battle-input2-btn" class="btn btn-primary navbar-btn" title="{{'New battle'|translate:'app'|escape}}" disabled>
                <span class="fa fa-pencil-square-o fa-fw"></span>
                Splatoon 2
              </button>
            </li>
            <li>
              <button id="battle-input-btn" class="btn btn-primary navbar-btn" title="{{'New battle'|translate:'app'|escape}}" disabled>
                <span class="fa fa-pencil-square-o fa-fw"></span>
                Splatoon
              </button>
            </li>
          </ul>
        {{/if}}
      </div>
    </div>
  </div>
</nav>
{{/strip}}
