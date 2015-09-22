{{strip}}
  <footer class="footer">
    <div class="container text-muted">
      <div class="footer-version">
        {{$_ver = \app\components\Version::getVersion()}}
        {{$_revL = \app\components\Version::getRevision()}}
        {{$_revS = \app\components\Version::getShortRevision()}}
        {{$app->name|escape}} Version <a href="https://github.com/fetus-hina/IkaLogLog/releases/tag/v{{$_ver|escape:url|escape}}">{{$_ver|escape}}</a>
        {{if $_revL && $_revS}}
          , Revision <a href="https://github.com/fetus-hina/IkaLogLog/commit/{{$_revL|escape:url|escape}}">{{$_revS|escape}}</a>
        {{/if}}
      </div>
      <div class="footer-author">
        Copyright &copy; 2015 AIZAWA Hina.&#32;
        <a href="https://twitter.com/fetus_hina" title="Twitter: fetus_hina" class="auto-tooltip">
          <span class="fa fa-twitter"></span>
        </a>&#32;<a href="https://github.com/fetus-hina" title="GitHub: fetus-hina" class="auto-tooltip">
          <span class="fa fa-github"></span>
        </a><br>
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
        このサイトのソースコードは<a href="https://github.com/fetus-hina/IkaLogLog">オープンソース(MIT License)です</a>。（※イラストを除く）<br>
        バグの報告・改善の提案などがありましたら、
          <a href="https://github.com/fetus-hina/IkaLogLog"><span class="fa fa-github"></span> GitHubのプロジェクト</a>に報告・提案するか、
          <a href="https://twitter.com/fetus_hina"><span class="fa fa-twitter"></span> @fetus_hina</a>にご連絡ください。
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
