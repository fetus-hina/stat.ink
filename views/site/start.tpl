{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'What\'s this?'|translate:'app'}}"}}
  <div class="container">
    <h1>
      {{'What\'s this?'|translate:'app'}}
    </h1>
    <p>
      このサイトは、スプラトゥーンの勝敗データを自動的に収集して統計として後で解析できるように蓄積するサイトです。
    </p>
    <p>
      まだ作り始めたばかりなのでほとんど何もできませんが、将来的にはグラフによる勝率変化の遷移などの様々な情報解析ができるようになる……つもりです。
    </p>
    <h2>
      使い方
    </h2>
    <p>
      まず、利用するには <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog</a> などの解析ソフトと、キャプチャデバイス（キャプチャボード）等が必要です。
    </p>
    <p>
      キャプチャデバイスの準備と、IkaLog (等)の対応ソフトを用意できれば、あとはこのサイトでユーザ登録をして、発行される「APIキー」を対応ソフトに登録すれば自動的にデータが収集されます。
    </p>
    <h3>
      高度な使い方
    </h3>
    <p>
      stat.ink は <a href="https://github.com/fetus-hina/stat.ink/blob/master/API.md">API を公開しています</a>ので、IkaLog に限らず、例えば自作のソフトウェアによっても登録が行えます。
    </p>
  </div>
{{/strip}}
