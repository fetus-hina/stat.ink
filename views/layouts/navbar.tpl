{{strip}}
  {{use class="app\models\Timezone"}}
  {{use class="app\models\Language"}}
<nav class="navbar navbar-inverse navbar-fixed-top">
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
                <span class="fa fa-user"></span> {{'Guest'|translate:'app'|escape}} <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="{{path route="user/login"}}">
                    <span class="fa fa-sign-in"></span> {{'Log In'|translate:'app'|escape}}
                  </a>
                </li>
                <li>
                  <a href="{{path route="user/register"}}">
                    <span class="fa fa-plus"></span> {{'Register'|translate:'app'|escape}}
                  </a>
                </li>
              </ul>
            {{else}}
              {{$ident = $user->identity}}
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-user"></span> {{$ident->name|escape}} <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="{{path route="show/user" screen_name=$ident->screen_name}}">
                    <span class="fa fa-user"></span> {{'Your Battles'|translate:'app'|escape}}
                  </a>
                </li>
                <li>
                  <a href="{{path route="user/profile"}}">
                    <span class="fa fa-gear"></span> {{'Settings'|translate:'app'|escape}}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="{{path route="user/logout"}}">
                    <span class="fa fa-sign-out"></span> {{'Log Out'|translate:'app'|escape}}
                  </a>
                </li>
              </ul>
            {{/if}}
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              Language <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              {{foreach Language::find()->orderBy('name ASC')->all() as $lang}}
                <li>
                  <a href="javascript:;" data-lang="{{$lang->lang|escape}}" class="language-change">
                    <span class="fa fa-check" style="{{if $app->language !== $lang->lang}}color:transparent{{/if}}"></span>&#32;
                    {{$lang->name|escape}} / {{$lang->name_en|escape}}
                  </a>
                </li>
              {{/foreach}}
            </ul>
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              {{'Time Zone'|translate:'app'|escape}} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              {{foreach Timezone::find()->all() as $tz}}
                <li>
                  <a href="javascript:;" data-tz="{{$tz->identifier|escape}}" class="timezone-change">
                    <span class="fa fa-check" style="{{if $app->timeZone !== $tz->identifier}}color:transparent{{/if}}"></span>&#32;
                    {{$tz->name|translate:'app'|escape}}
                  </a>
                </li>
              {{/foreach}}
            </ul>
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              {{'Link'|translate:'app'|escape}} <span class="caret"></span>
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
                <a href="https://twitter.com/splatoonjp"><span class="fa fa-twitter"></span> {{'Official Twitter (Japan)'|translate:'app'|escape}}</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://splatoon.nintendo.net/">{{'SplatNet'|translate:'app'|escape}}</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://fest.ink/">イカフェスレート</a>
              </li>
              <li>
                <a href="https://ikadenwa.ink/">イカデンワ</a>
              </li>
              <li>
                <a href="http://ikazok.net/">イカナカマ</a>
              </li>
              <li>
                <a href="http://siome.ink/">siome</a>
              </li>
              <li>
                <a href="http://ika.akaihako.com/unislot">ウニスロット</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
{{/strip}}
