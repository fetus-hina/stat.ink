{{strip}}

{{title}}{{$app->name|escape}} | フェス「{{$fest->name|escape}}」の推定勝率{{/title}}

<div class="container" data-fest="{{$fest->id|escape}}">
  <div id="social">
    <a class="twitter-share-button" data-text="フェス「{{$fest->name|escape}}」の推定勝率" data-url="{{url route="/fest/view" id=$fest->id}}" data-hashtags="Splatoon,Splatfest,スプラトゥーン" data-count="horizontal" data-via="ikafest" href="https://twitter.com/intent/tweet">Tweet</a>
    &#32;
    <a class="twitter-follow-button" data-show-count="false" href="https://twitter.com/ikafest">Follow @ikafest</a>
  </div>

  <div class="btn-toolbar" role="toolbar">
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default auto-tooltip" id="btn-update" title="表示データを今すぐ更新します">
        <span class="glyphicon glyphicon-refresh"></span>
      </button>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default auto-tooltip" id="btn-autoupdate" title="自動更新のオンオフを切り替えます">
        <span class="glyphicon glyphicon-time"></span>
      </button>
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-default dropdown-toggle auto-tooltip" id="btn-update-interval" title="自動更新間隔を設定します" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" id="dropdown-update-interval">
          {{if false}}
            <li><a href="javascript:;" class="update-interval" data-interval="5"><span class="glyphicon glyphicon-ok"></span> 5秒ごと</a></li>
            <li><a href="javascript:;" class="update-interval" data-interval="10"><span class="glyphicon glyphicon-ok"></span> 10秒ごと</a></li>
          {{/if}}
          <li><a href="javascript:;" class="update-interval" data-interval="120"><span class="glyphicon glyphicon-ok"></span> 2分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="300"><span class="glyphicon glyphicon-ok"></span> 5分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="600"><span class="glyphicon glyphicon-ok"></span> 10分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="900"><span class="glyphicon glyphicon-ok"></span> 15分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="1200"><span class="glyphicon glyphicon-ok"></span> 20分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="1800"><span class="glyphicon glyphicon-ok"></span> 30分ごと</a></li>
          <li><a href="javascript:;" class="update-interval" data-interval="3600"><span class="glyphicon glyphicon-ok"></span> 60分ごと</a></li>
        </ul>
      </div>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default btn-graphtype auto-tooltip" title="両チームの勝率を上下に並べて表示します" data-type="stack">
        <span class="fa fa-area-chart"></span> 上下
      </button>
      <button type="button" class="btn btn-default btn-graphtype auto-tooltip" title="両チームの勝率を重ねて表示します" data-type="overlay">
        <span class="fa fa-area-chart"></span> 重ね
      </button>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default auto-tooltip" id="btn-ink-color" title="インク色の使用有無を切り替えます">
        <span class="glyphicon glyphicon-tint"></span>
      </button>
      <button type="button" class="btn btn-default auto-tooltip" id="btn-scale" title="試合開催数を推定し、補正して表示します（実験的）">
        <span class="glyphicon glyphicon-adjust"></span>
      </button>
    </div>
  </div>

  <div id="official-result">
    <h1>
      フェス「{{$fest->name|escape}}」の結果
    </h1>
    <div id="official-result-container" class="ikamodoki">
    </div>
    <hr>
  </div>

  <h1>
    フェス「{{$fest->name|escape}}」の推定勝率
  </h1>
  <p>
    スプラトゥーンの公式サイトで公開されているデータを基に推計したデータです。
    数パーセントポイント程度の誤差を含んでいるものとして参考程度にどうぞ。
  </p>

  <h2 id="rate">
    推定勝率: <span class="ikamodoki"><span class="total-rate" data-team="alpha">シュトクチュウ</span> VS <span class="total-rate" data-team="bravo">シュトクチュウ</span></span>
  </h2>
  <p>
    {{$fest->alphaTeam->name|escape}}チーム: <span class="total-rate" data-team="alpha">取得中</span>（サンプル数：<span class="sample-count" data-team="alpha">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-danger progress-bar-striped total-progressbar" style="width:0%" data-team="alpha">
    </div>
  </div>
  <p>
    {{$fest->bravoTeam->name|escape}}チーム: <span class="total-rate" data-team="bravo">取得中</span>（サンプル数：<span class="sample-count" data-team="bravo">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-success progress-bar-striped total-progressbar" style="width:0%" data-team="bravo">
    </div>
  </div>

  <h2 id="graph-short">
    短期的勝率グラフ
  </h2>
  <p>
    その時点での直近の勝率をグラフにしたものです。「この時間帯はどっちが優勢」ということを示します。
  </p>
  <div class="rate-graph rate-graph-short">
  </div>

  <h2 id="graph-whole">
    長期的勝率グラフ
  </h2>
  <p>
    上部の「推定勝率」の遷移をグラフにしたものです。「最終的にどちらが勝ちそうか」ということを示します。
  </p>
  <div class="rate-graph rate-graph-whole">
  </div>

  <h2 id="graph-win">
    勝利数グラフ
  </h2>
  <p>
    数値はサンプリングされたものです。全数のどのくらいの割合で取得できているのかはわかりません。（サンプル数: <span class="sample-count">(取得中)</span>）
  </p>
  <div class="rate-graph rate-graph-win-count">
  </div>

  <h2 id="about-data">
    表示している情報
  </h2>
  <p>
    フェス開催期間: {{$fest->start_at|date_format:'%Y-%m-%d %H:%M %Z'|escape}} ～ {{$fest->end_at|date_format:'%Y-%m-%d %H:%M %Z'|escape}}
  </p>
  <p>
    <span title="サーバが任天堂から最後にデータを取得したタイミングです">データ最終更新: <span class="last-updated-at">(取得中)</span></span>、
    <span title="あなた（ブラウザ）が fest.ink のサーバから最後にデータを取得したタイミングです">データ最終取得: <span class="last-fetched-at">(取得中)</span></span>、
    サンプル数: <span class="sample-count">(取得中)</span>
  </p>

  {{include '@app/views/fest/attention.tpl'}}
</div>
{{/strip}}
