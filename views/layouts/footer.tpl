{{strip}}
  <footer class="footer">
    <div class="container text-muted">
      <div class="footer-version">
        {{$_ver = \app\components\Version::getVersion()}}
        {{$_revL = \app\components\Version::getRevision()}}
        {{$_revS = \app\components\Version::getShortRevision()}}
        {{$app->name|escape}} Version {{$_ver|escape}}
        {{if $_revL && $_revS}}
          , Revision <a href="https://github.com/fetus-hina/stat.ink/tree/{{$_revL|escape:url|escape}}">{{$_revS|escape}}</a>
        {{/if}}
      </div>
      <div class="footer-author">
        Copyright &copy; 2015-2016 AIZAWA Hina.&#32;
        <a href="https://twitter.com/fetus_hina" title="Twitter: fetus_hina" class="auto-tooltip">
          <span class="fa fa-twitter"></span>
        </a>&#32;<a href="https://github.com/fetus-hina" title="GitHub: fetus-hina" class="auto-tooltip">
          <span class="fa fa-github"></span>
        </a><br>
      </div>
      <div class="footer-nav">
        <a href="{{path route="/site/api"}}">{{'API'|translate:'app'|escape}}</a>
        &#32;|&#32;
        <a href="{{path route="/site/privacy"}}">{{'Privacy Policy'|translate:'app'|escape}}</a>
        &#32;|&#32;
        <a href="{{path route="/site/license"}}">{{'Open Source Licenses'|translate:'app'|escape}}</a>
      </div>
      <div class="footer-notice">
        {{'This website is an UNOFFICIAL SERVICE. It is not related to the Splatoon development team or Nintendo.'|translate:'app'|escape}}<br>
        {{'This website is an open source project. It is under the MIT License. The source code is available on GitHub.'|translate:'app'|escape}}&#32;
          <a href="https://github.com/fetus-hina/stat.ink"><span class="fa fa-github"></span></a><br>
        {{'Feedback or suggestions are welcome. Please contact me via GitHub or Twitter.'|translate:'app'|escape}}&#32;
          <a href="https://github.com/fetus-hina/stat.ink"><span class="fa fa-github"></span></a>&#32;
          <a href="https://twitter.com/fetus_hina"><span class="fa fa-twitter"></span></a>
      </div>
      <div class="footer-powered">
        {{$_phpv = phpversion()}}{{* PHP コード開始タグと解釈される問題があるので一回変数に入れる *}}
        {{'Powered by'|translate:'app'|escape}}&#32;
        <a href="http://www.yiiframework.com/">Yii Framework {{\Yii::getVersion()|escape}}</a>,&#32;
        <a href="http://php.net/">PHP {{$_phpv|escape}}</a>
      </div>
    </div>
  </footer>
{{/strip}}
