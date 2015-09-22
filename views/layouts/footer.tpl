{{strip}}
  {{\app\assets\PixivBannerAsset::register($this)|@void}}
  {{$assetManager = $app->assetManager}}
  {{$pixivAsset = $assetManager->getBundle('app\assets\PixivBannerAsset')}}
  <footer class="footer">
    <div class="container text-muted">
      <div class="footer-version">
        {{$_ver = \app\components\Version::getVersion()}}
        {{$_revL = \app\components\Version::getRevision()}}
        {{$_revS = \app\components\Version::getShortRevision()}}
        {{$app->name|escape}} Version <a href="https://github.com/fetus-hina/fest.ink/releases/tag/v{{$_ver|escape:url|escape}}">{{$_ver|escape}}</a>
        {{if $_revL && $_revS}}
          , Revision <a href="https://github.com/fetus-hina/fest.ink/commit/{{$_revL|escape:url|escape}}">{{$_revS|escape}}</a>
        {{/if}}
      </div>
      <div class="footer-author">
        Copyright &copy; 2015 AIZAWA Hina.&#32;
        <a href="https://twitter.com/fetus_hina" title="Twitter: fetus_hina" class="auto-tooltip">
          <span class="fa fa-twitter"></span>
        </a>&#32;<a href="https://github.com/fetus-hina" title="GitHub: fetus-hina" class="auto-tooltip">
          <span class="fa fa-github"></span>
        </a><br>
        Illustrator: ちょまど.&#32;
        <a href="https://twitter.com/chomado" title="Twitter: chomado" class="auto-tooltip">
          <span class="fa fa-twitter"></span>
        </a>&#32;<a href="https://github.com/chomado" title="GitHub: chomado" class="auto-tooltip">
          <span class="fa fa-github"></span>
        </a>&#32;<a href="http://chomado.com/" title="ちょまど帳" class="auto-tooltip">
          <span class="fa fa-globe"></span>
        </a>&#32;<a href="http://www.amazon.co.jp/%E3%81%A1%E3%82%87%E3%81%BE%E3%81%A9/e/B00WPPKOV8/?_encoding=UTF8&amp;camp=247&amp;creative=1211&amp;linkCode=ur2&amp;tag=fetusjp-22" title="Amazon: 著者ページ" class="auto-tooltip">
          <span class="fa fa-amazon"></span>
        </a>&#32;<a href="http://www.pixiv.net/member.php?id=6783972" title="Pixiv: #6783972" class="auto-tooltip">
          <img src="{{$assetManager->getAssetUrl($pixivAsset, 'pixiv_logo.png')|escape}}" style="height:1em;width:auto">
        </a>
      </div>
      <div class="footer-nav">
        <a href="{{path route="/site/api"}}">API</a>
        &#32;|&#32;
        <a href="{{path route="/site/privacy"}}">プライバシーポリシー</a>
        &#32;|&#32;
        <a href="{{path route="/site/license"}}">オープンソースライセンス</a>
      </div>
      <div class="footer-notice">
        このサイトは非公式(unofficial)サービスです。任天堂株式会社とは一切関係ありません。<br>
        このサイトの内容は無保証です。必ず公式情報をお確かめください。<br>
        このサイトのソースコードは<a href="https://github.com/fetus-hina/fest.ink">オープンソース(MIT License)です</a>。（※イラストを除く）<br>
        バグの報告・改善の提案などがありましたら、
          <a href="https://github.com/fetus-hina/fest.ink"><span class="fa fa-github"></span> GitHubのプロジェクト</a>に報告・提案するか、
          <a href="https://twitter.com/fetus_hina"><span class="fa fa-twitter"></span> @fetus_hina</a>にご連絡ください。<br>
        ページの一部に<a href="http://aramugi.com/?page_id=807" class="ikamodoki">イカモドキ</a>と<a href="http://fizzystack.web.fc2.com/paintball.html" class="ikamodoki">Paintball</a>を使用しています。<br>
        サイト内で表示している日時の時間帯は上部の「タイムゾーン」の設定に従っています。通常日本時間(<code>Asia/Tokyo</code>)で表示しています。現在の設定は<code>{{$app->getTimezone()|escape}}</code>です。<br>
      </div>
      <div class="footer-powered">
        {{$_phpv = phpversion()}}{{* PHP コード開始タグと解釈される問題があるので一回変数に入れる *}}
        Powered by&#32;
        <a href="http://www.yiiframework.com/">Yii Framework {{\Yii::getVersion()|escape}}</a>,&#32;
        <a href="http://php.net/">PHP {{$_phpv|escape}}</a>
      </div>
    </div>
  </footer>
{{/strip}}
