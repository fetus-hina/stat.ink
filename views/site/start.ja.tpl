{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Getting Started'|translate:'app'}}"}}

  {{\app\assets\AboutAsset::register($this)|@void}}
  {{$aboutAsset = $app->assetManager->getBundle('app\assets\AboutAsset')}}

  <div class="container">
    <h1>
      {{'Getting Started'|translate:'app'|escape}}
    </h1>
    <p>
      {{'This website collect your Splatoon logs, and analyze it.'|translate:'app-start'|escape}}
    </p>

    {{AdWidget}}
    {{SnsWidget}}

    <h2>
      {{'How to collect your log'|translate:'app-start'|escape}}
    </h2>
    <p>
      {{'There are two ways. "Automatic" or "manually."'|translate:'app-start'|escape}}
    </p>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <h3>
          {{'Automatic (Recommended)'|translate:'app-start'|escape}}
        </h3>
        <p>
          <a href="https://github.com/hasegaw/IkaLog/wiki/ja_WinIkaLog">IkaLog</a>などの対応ソフトを利用して自動的に戦績を登録する方法です。
        </p>
        <p>
          IkaLogの場合、Wii Uからテレビへ出力している映像信号のコピーをPCに入力することでプレーデータを解析します。
          次のようなイメージです。
        </p>
        <img src="{{$app->assetmanager->getAssetUrl($aboutAsset, 'overview.ja.png')|escape}}" alt="" title="" style="width:100%;max-width:530px">
        <p>
          アプリケーションが動作していれば自動的・正確に多数のデータが{{$app->name|escape}}に送信されてきます。
          この利用ケースが{{$app->name|escape}}の能力を最大限に生かせます。
        </p>
        <p>
          反面、HDMIに対応したキャプチャデバイスを保有していない場合は「初期費用」がかかります。
        </p>
        <p>
          ※一部のキャプチャデバイス（例えばAVT-C875）はHDMIスプリッタを内蔵していることがあります。
        </p>
        <p>
          ※720pの解像度を扱えないキャプチャは利用できません。
        </p>
        <p>
          ※IkaLogは全てのキャプチャデバイスで動作するわけではありません。
          キャプチャデバイスによって数ピクセルずれて表示される、色味がおかしいなどが発生することが経験上わかっています。
          新たに購入する場合は<a href="https://github.com/hasegaw/IkaLog/wiki/CaptureDevices">IkaLog Wikiの確認済みリスト</a>を確認して購入することをおすすめします。
          確認済みリストに掲載があるものでも必ず使えるとは限らないことにご注意ください。
        </p>
        <p>
          <a href="{{url route="site/kamiup"}}">IkaLogと組み合わせてよく使われるAVT-C875"神うｐ"の接続が（よく使われる割に特殊な接続になるので）わかりづらいと思われるので簡単に説明を書きました。</a>
        </p>
        <hr>
        <p>
          現在この方法で利用できるアプリケーションのリスト:
        </p>
        <ul>
          <li>
            <a href="https://github.com/hasegaw/IkaLog/wiki/ja_WinIkaLog">IkaLog / WinIkaLog</a> (Windows, Mac, Linux)
          </li>
        </ul>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <h3>
          {{'Manually'|translate:'app-start'|escape}}
        </h3>
        <p>
          <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec&amp;hl=ja">イカレコ</a>などの対応ソフトを利用して手動で戦績を登録する方法です。（stat.ink のウェブからも登録できますが、あまりおすすめしません）
        </p>
        <p>
          この方法は至ってシンプルで、画面に表示されたリザルト画面を基に利用者が手動で登録します。
        </p>
        <p>
          限られた時間の中で登録することを前提としているため、最低限の情報のみが記録されることになると思います。
        </p>
        <hr>
        <p>
          現在この方法で利用できるアプリケーションのリスト:
        </p>
        <ul>
          <li>
            stat.ink web client
          </li>
          <li>
            <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec&amp;hl=ja">イカレコ</a> (Android)
          </li>
        </ul>
      </div>
    </div>

    <h2>
      利用方法の例
    </h2>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <h3>
          IkaLogの場合
        </h3>
        <p>
          キャプチャデバイスの設定等は<a href="https://github.com/hasegaw/IkaLog/wiki/ja_WinIkaLog">IkaLogの説明</a>通りに行えているものとします。
          Previewにゲーム画面が出るまではそちらを見て設定してください。
        </p>
        <ol>
          <li>
            <a href="{{url route="user/register"}}">{{$app->name|escape}}へのユーザ登録</a>を行ってください。
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
          これで（IkaLogが動いていれば）自動的にデータが送信されます。
        </p>
        <p>
          ※IkaLogはできるだけ最新のものを使うようにしてください。
        </p>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <h3>
          イカレコの場合
        </h3>
        <p>
          <a href="http://gigazine.net/news/20151217-splatoon-ikarec/">Gigazine様にてイカレコが紹介された時の記事</a>にアプリの紹介からstat.inkの連携まで詳しく記載されていますので、そちらを参照してください。
        </p>
      </div>
    </div>

    <h2>
      高度な使い方
    </h2>
    <p>
      {{$app->name|escape}}は<a href="https://github.com/fetus-hina/stat.ink/blob/master/API.md">APIを公開しています</a>ので、自作のソフトウェアによっても登録が行えます。
    </p>
    <p>
      もちろんあなた専用のアプリを作成しても構いませんし、広く公開すると喜ぶ人がいるかもしれません。
    </p>

    <hr>

    <p>
      このサイトは、相沢陽菜 &lt;hina@bouhime.com&gt; (<span class="fa fa-twitter left"></span>fetus_hina, <span class="fa fa-github left"></span>fetus-hina) が個人的に作成したものです。
      任天堂株式会社とは一切関係はありません。
      任天堂株式会社へこのサイトやIkaLogのことを問い合わせたりはしないでください。単純に迷惑になります。
    </p>
    <p>
      このサイトのソースコードはMIT Licenseに基づくオープンソースソフトウェアとして公開しています。
      MIT Licenseの範囲内で誰でも自由に改造・改良・フォークを行うことができます。
    </p>
  </div>
{{/strip}}
