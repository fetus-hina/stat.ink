{{strip}}
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
                <span class="fa fa-user"></span> ゲスト <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="{{path route="user/login"}}">ログイン</a>
                </li>
                <li>
                  <a href="{{path route="user/register"}}">ユーザ登録</a>
                </li>
              </ul>
            {{else}}
              {{$ident = $user->identity}}
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-user"></span> {{$ident->name|escape}} さん <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a href="{{path route="user/profile"}}">ユーザ情報</a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="{{path route="user/logout"}}">ログアウト</a>
                </li>
              </ul>
            {{/if}}
          </li>
          <li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              リンク <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                <a href="http://www.nintendo.co.jp/wiiu/agmj/">スプラトゥーン 公式サイト</a>
              </li>
              <li>
                <a href="https://twitter.com/splatoonjp">
                  <span class="fa fa-twitter"></span> スプラトゥーン 公式ツイッター
                </a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://splatoon.nintendo.net/">イカリング</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="https://fest.ink/">イカフェスレート</a>
              </li>
              <li>
                <a href="https://ikadenwa.ink/" class="auto-tooltip" title="フレンドマッチ中の通話に便利なサイトです">イカデンワ</a>
              </li>
              <li>
                <a href="http://ikazok.net/" class="auto-tooltip" title="チームの結成や管理、交流に便利なサイトです">イカナカマ</a>
              </li>
              <li>
                <a href="http://siome.ink/" class="auto-tooltip" title="TwitterアカウントとニンテンドーネットワークIDを登録して告知できるサイトです">siome</a>
              </li>
              <li>
                <a href="http://ika.akaihako.com/unislot" class="auto-tooltip" title="サザエガチャのシミュレータです">ウニスロット</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
{{/strip}}
