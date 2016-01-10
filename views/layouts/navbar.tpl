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
        <a class="navbar-brand" href="/">{{$app->name|escape}}</a>
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
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
                    <span class="fa fa-sign-in left"></span>{{'Login'|translate:'app'|escape}}
                  </a>
                </li>
                <li>
                  <a href="{{path route="user/register"}}">
                    <span class="fa fa-plus left"></span>{{'Register'|translate:'app'|escape}}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="javascript:;" id="toggle-color-lock">
                    <span class="fa fa-check left"></span>{{'Color-Blind Support'|translate:'app'|escape}}
                  </a>
                </li>
              </ul>
            {{else}}
              {{$ident = $user->identity}}
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-user left"></span>{{$ident->name|escape}}&#32;<span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="{{path route="show/user" screen_name=$ident->screen_name}}">
                    <span class="fa fa-user left"></span>{{'Your Battles'|translate:'app'|escape}}
                  </a>
                </li>
                <li>
                  <a href="{{path route="user/profile"}}">
                    <span class="fa fa-gear left"></span>{{'Settings'|translate:'app'|escape}}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="{{path route="user/logout"}}">
                    <span class="fa fa-sign-out left"></span>{{'Logout'|translate:'app'|escape}}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="javascript:;" id="toggle-color-lock">
                    <span class="fa fa-check left"></span>{{'Color-Blind Support'|translate:'app'|escape}}
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
                  <a href="javascript:;" data-lang="{{$lang->lang|escape}}" class="language-change">
                    <span class="fa fa-check left" style="{{if $app->language !== $lang->lang}}color:transparent{{/if}}"></span>
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
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <span class="fa fa-clock-o left"></span>
              {{'Time Zone'|translate:'app'|escape}}&#32;<span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              {{foreach Timezone::find()->with('countries')->all() as $tz}}
                <li>
                  <a href="javascript:;" data-tz="{{$tz->identifier|escape}}" class="timezone-change">
                    <span class="fa fa-check left" style="{{if $app->timeZone !== $tz->identifier}}color:transparent{{/if}}"></span>
                    {{if $tz->countries}}
                      {{foreach $tz->countries as $cc}}
                        <span class="flag-icon flag-icon-{{$cc->key|escape}}"></span>&#32;
                      {{/foreach}}
                    {{/if}}
                    {{$tz->name|translate:'app-tz'|escape}}
                  </a>
                </li>
              {{/foreach}}
            </ul>
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <span class="fa fa-link left"></span>
              {{'Links'|translate:'app'|escape}}&#32;<span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                <a href="http://www.nintendo.co.jp/wiiu/agmj/">{{'Splatoon Official Website (Japan)'|translate:'app'|escape}}</a>
              </li>
              <li>
                <a href="http://splatoon.nintendo.com/">{{'Splatoon Official Website (US/Canada)'|translate:'app'|escape}}</a>
              </li>
              <li>
                <a href="https://www.nintendo.co.uk/Games/Wii-U/Splatoon-892510.html">{{'Splatoon Official Website (UK)'|translate:'app'|escape}}</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://twitter.com/splatoonjp">
                  <span class="fa fa-twitter left"></span>
                  {{'Official Twitter (Japan)'|translate:'app'|escape}}
                </a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://splatoon.nintendo.net/">{{'SplatNet'|translate:'app'|escape}}</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">
                  {{'IkaLog'|translate:'app'|escape}}
                  （<span class="fa fa-windows left"></span>
                  <span class="fa fa-apple left"></span>
                  <span class="fa fa-linux"></span>）
                </a>
              </li>
              <li>
                <a href="https://dl.dropboxusercontent.com/u/14421778/IkaLog/download.html">
                  └ {{'IkaLog Download Page'|translate:'app'|escape}}（<span class="fa fa-windows"></span>）
                </a>
              </li>
              <li>
                <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec">
                  {{'IkaRec'|translate:'app'|escape}}（<span class="fa fa-android"></span>）
                </a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://fest.ink/">{{'fest.ink'|translate:'app'|escape}}</a>
              </li>
              <li>
                <a href="https://ikadenwa.ink/">{{'Ika-Denwa'|translate:'app'|escape}}</a>
              </li>
              <li>
                <a href="http://ikazok.net/">{{'Ika-Nakama'|translate:'app'|escape}}</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
{{/strip}}
