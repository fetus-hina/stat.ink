{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'What\'s this?'|translate:'app'}}"}}

  {{\app\assets\AboutAsset::register($this)|@void}}
  {{$aboutAsset = $app->assetManager->getBundle('app\assets\AboutAsset')}}

  <div class="container">
    <h1>
      {{'What\'s this?'|translate:'app'|escape}}
    </h1>
    <p>
      このサイトは、スプラトゥーンの勝敗データを自動的に収集して統計として後で解析できるように蓄積するサイトです。
    </p>

    <h2>
      概要
    </h2>
    <p>
      Wii U から stat.ink までの接続イメージです。
    </p>
    <img src="{{$app->assetmanager->getAssetUrl($aboutAsset, 'overview.png')|escape}}" alt="" title="" style="width:100%;max-width:530px">
    <p>
      Wii U からテレビへ流している映像イメージをPCにも入力して、stat.ink への送信に対応した解析ソフト（<a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog</a> 等）にかけます。
    </p>
    <p>
      解析ソフトはプレー画面を解析して、試合終了時に stat.ink に試合データを自動的に送信します。
    </p>
    <p>
      正常に動作していれば、普通にプレーするだけで自動的に戦績が記録されることになります。
    </p>
    <p>
      ※一部のキャプチャデバイス（例えば AVT-C875）はHDMIスプリッタを内蔵していることがあります。
    </p>
    <p>
      ※IkaLog などの解析ソフトはすべてのキャプチャデバイスに対応しているわけではありません。新規に購入する場合、動作確認済みのデバイスを購入することをおすすめします。
    </p>

    <h2>
      対応解析ソフト
    </h2>
    <ul>
      <li>
        <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog / IkaLog GUI</a> (Windows, Mac。原理上はほかのOSでも）
      </li>
      <li>
        対応ソフト募集中（IkaLogに多大な影響をうけ、IkaLogと共同で開発を行った面がありますが、IkaLog専用ではありません）
      </li>
    </ul>

    <h2>
      対応キャプチャデバイス
    </h2>
    <p>
      IkaLog を使用される場合、<a href="https://github.com/hasegaw/IkaLog/wiki/CaptureDevices">IkaLog Wiki</a> の動作報告をご覧ください。
    </p>
    <p>
      ※ここに記載があるデバイスでも動作するとは限りません。また、動作確認済みとされているものでも設定等によってブキの誤認識をする場合があります。
    </p>

    <h2>
      IkaLog での使用方法
    </h2>
    <p>
      キャプチャデバイスの設定等は <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog の説明</a>通りに行えているものとします。
      Preview にゲーム画面が出るまではそちらを見て設定してください。
    </p>
    <ol>
      <li>
        <a href="{{url route="user/register"}}">stat.ink へのユーザ登録</a>を行ってください。
        ユーザ登録が既にお済みでしたらログインしてください。
      </li>
      <li>
        <a href="{{url route="user/profile"}}">プロフィールと設定</a>画面を開きます。
      </li>
      <li>
        「APIキー」のボタンを押し、 <code>ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefg</code> のような API キーを表示します。
        （このAPIキーはあなた専用のもので、パスワードと同じくらい重要なものです。他人には教えないでください）
      </li>
      <li>
        IkaLog の Options - stat.ink を開きます。
      </li>
      <li>
        先ほどのAPIキーを、専用の入力欄に貼り付けます。
      </li>
      <li>
        「☑ stat.inkへのスコアを送信する」にチェックを入れます。
      </li>
      <li>
        「Apply」ボタンを押して設定を適用します。
      </li>
    </ol>
    <p>
      これで（IkaLog が動いていれば）自動的にデータが送信されます。
    </p>
    <p>
      ※IkaLog はできるだけ最新のものを使うようにしてください。
    </p>

    <h2>
      高度な使い方
    </h2>
    <p>
      stat.ink は <a href="https://github.com/fetus-hina/stat.ink/blob/master/API.md">API を公開しています</a>ので、IkaLog に限らず、例えば自作のソフトウェアによっても登録が行えます。
    </p>

    <hr>

    <p>
      このサイトは、相沢陽菜 &lt;hina@bouhime.com&gt; (<span class="fa fa-twitter"></span> fetus_hina, <span class="fa fa-github"></span> fetus-hina) が個人的に作成したものです。
      任天堂株式会社とは一切関係はありません。
      任天堂株式会社へこのサイトや IkaLog のことを問い合わせたりはしないでください。単純に迷惑になります。
    </p>
    <p>
      このサイトのソースコードは MIT License に基づくオープンソースソフトウェアとして公開しています。
      MIT License の範囲内で誰でも自由に改造・改良・フォークを行うことができます。
    </p>
  </div>
{{/strip}}
