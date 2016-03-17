{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'FAQ'|translate:'app'}}"}}

  {{\app\assets\AboutAsset::register($this)|@void}}
  {{$aboutAsset = $app->assetManager->getBundle('app\assets\AboutAsset')}}

  <div class="container">
    <h1>
      {{'FAQ'|translate:'app'|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <p>
      IkaLogの動作や認識に関する問題は<a href="https://github.com/hasegaw/IkaLog/wiki/ja_FAQ">IkaLog FAQ</a>をご覧ください。
    </p>

    <h2>
      Q: stat.ink はどのようなサービスですか
    </h2>
    <p>
      A: stat.ink は Splatoon の戦績を集めて集計するウェブサービスです。戦績の統計には、<a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog(Windows, Mac, Linux)</a>や<a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec&amp;hl=ja">イカレコ(Android)</a>などの対応アプリが必要となります。
    </p>
    <p>
      Splatoon Advent Calendar 2015 で解説を書きましたのでご覧ください： <a href="https://blog.fetus.jp/201512/30.html">イカフェスレートとstat.ink</a>
    </p>
    <p>
      <img src="{{$app->assetmanager->getAssetUrl($aboutAsset, 'ecosystem.png')|escape}}" alt="" title="">
    </p>

    <h2>
      Q: stat.ink 自体に戦績登録機能はないのですか
    </h2>
    <p>
      A: 現時点ではありません。<a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog(Windows, Mac, Linux)</a>や<a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec&amp;hl=ja">イカレコ(Android)</a>をご利用ください。
    </p>

    <h2>
      Q: 対応ソフトについて教えてください
    </h2>
    <p>
      A: 現時点でこちらで把握している対応ソフトはIkaLogとイカレコの二つのみです。環境や好みに応じて使ってください。
    </p>
    <ul>
      <li>
        <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog</a><br>
        キャプチャデバイスを使用して画面に表示される情報を解析するソフトウェアです。WindowsやMac、Linuxで動きます。<br>
        自動で解析・登録されるため運用は非常に簡単でおすすめです。<br>
        半面、キャプチャデバイスの準備が必要、キャプチャデバイスによっては動作しないなどあるため万人がすぐに使えるというものではありません。<br>
        ※stat.ink以外にも利用できます。例えばTwitter投稿などができます。
      </li>
      <li>
        <a href="https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec&amp;hl=ja">イカレコ</a><br>
        Androidで動作する戦績登録用のアプリです。<br>
        手動で登録する必要がありますが、キャプチャデバイス等を準備する必要はないためある意味ではお手軽です。<br>
        ※stat.ink連携なしでも戦績の記録、閲覧、統計は出ます。
      </li>
    </ul>

    <h2>
      Q: iOS(iPhone)…
    </h2>
    <p>
      A: 誰かが作れば使えます。
    </p>

    <h2>
      Q: キャプチャデバイスやAndroid端末なしで利用できるサイトはありませんか
    </h2>
    <p>
      A: <a href="http://ika.lealog.net/">ウデマエアーカイブ</a>などが使えるかもしれません。手書きや Excel でも充分かもしれません。
    </p>

    <h2>
      Q: いろんな名前が出てきて混乱します
    </h2>
    <p>
      A: 複数のアプリが連携して動作しているので混乱してしまうのだと思います。簡単に整理します。冒頭の概念図も参照してください（概念図のブロックひとつひとつが別のアプリです）。
    </p>
    <dl>
      <dt>
        IkaLog
      </dt>
      <dd>
        スプラトゥーンの画像を解析するソフト（アプリケーション）の名前です。<br>
        今となっては「stat.inkと強い関連がある」といっても過言ではないと思いますが、あくまで別のアプリケーションです。<br>
        stat.inkと連携しなくても使えます。<br>
        <br>
        ※現在stat.inkとIkaLogは構造上（というかエコシステム上）お互いに割と強く依存していますが、それでも別のアプリケーションです。<br>
        <a href="https://github.com/hasegaw/IkaLog/wiki/ja_FAQ#ikalog-%E3%81%A8-statink-%E3%81%AE%E9%96%A2%E4%BF%82%E3%81%AF">IkaLog FAQの「IkaLog と stat.ink の関係は？」</a>も参照してください。</a>

      </dd>

      <dt>
        WinIkaLog
      </dt>
      <dd>
        IkaLogをWindowsでそこそこ簡単に<sup>[要出典]</sup>扱えるようにしたIkaLogの配布形態とそのアプリです。<br>
        IkaLogの全機能を使えるわけではありませんが、黒い画面と戦う時間は減ります。<br>
        <a href="https://github.com/hasegaw/IkaLog/wiki/ja_FAQ#ikalog-%E3%81%A8-winikalog-%E3%81%AF%E4%BD%95%E3%81%8C%E9%81%95%E3%81%84%E3%81%BE%E3%81%99%E3%81%8B">IkaLog FAQ の「IkaLog と WinIkaLog は何が違いますか？」</a>も参照してください。
      </dd>

      <dt>
        イカログ
      </dt>
      <dd>
        この言葉はとても曖昧です。<br>
        1. IkaLogのこと。<br>
        2. stat.inkのことを指してこう呼ばれていることが稀によくあります。（stat.inkがバトルの記録をこう呼んでいる箇所があることに加え、IkaLogとstat.inkの区別がついていない人もいるようです）<br>
        3. その他（イラスト方面？）。「イカログ民」という言葉をよく見かけますがよくわかりません。
      </dd>

      <dt>
        イカレコ
      </dt>
      <dd>
        Android用のアプリの名前です(iPhoneでは使えません)。自分のキル・デス等を手動で入力して利用します。<br>
        イカレコはそれ単体である程度の統計機能を備えていて、「stat.inkとの連携<strong>も</strong>できる」という立ち位置です。<br>
        もっとも、統計機能に関してはこんな考え方みたいです(<a href="https://twitter.com/ika_rec/status/676901615949991936">1</a>, <a href="https://twitter.com/ika_rec/status/676908380120858624">2</a>)。<br>
        単体で使えます。<br>
        stat.inkとの連携方法については<a href="http://ikarec.hatenablog.jp/entry/2015/12/18/010159">イカレコ開発ブログ</a>を参照してください。
      </dd>

      <dt>
        IkaRec
      </dt>
      <dd>
        イカレコのことです。
      </dd>

      <dt>
        イカキロク
      </dt>
      <dd>
        iPhone用のアプリの名前です(Androidでは使えません）。イカレコとよく似ています（実際の経緯はイカキロクが先）。
        <strong>stat.ink連携機能はありません</strong>。
      </dd>
    </dl>

    <h2>
      Q: 私は○○のブキを使っているのに違うブキに認識されます（その他誤認識関係）
    </h2>
    <p>
      A: 認識ソフト側の問題です。大体の場合はお使いの環境に依存した問題です。
    </p>
    <p>
      stat.ink では「ルール `nawabari`、ステージ `negitoro`、ブキ `wakaba`、結果 `win`」のように具体的な内容を受け取る仕様になっています。認識を間違えるとしたら stat.ink へデータを送信してくるソフト側の問題になります。
    </p>
    <p>
      IkaLog に限れば、入力が 720p でない場合に問題が発生しやすいようです。Wii U 本体の出力設定が 720p になっていることを確認してください。
    </p>
    <p>
      キャプチャデバイスによっては本来の入力から数ピクセルずれるなどの問題もあるようです。 IkaLog はある程度頑張って処理するようですが、本来の入力が与えられた場合に比べるとやはり劣るようです。
    </p>
    <p>
      <img src="{{$app->assetmanager->getAssetUrl($aboutAsset, 'ikalog-function.png')|escape}}" alt="" title="">
    </p>
    <iframe src="//www.slideshare.net/slideshow/embed_code/key/GZsPURQDtXaD9j" width="595" height="485" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="border:1px solid #CCC; border-width:1px; margin-bottom:5px; max-width: 100%;" allowfullscreen> </iframe> <div style="margin-bottom:5px"> <strong> <a href="//www.slideshare.net/TakeshiHasegawa1/ikalog-presentation-v13" title="IkaLog Presentation v1.3" target="_blank">IkaLog Presentation v1.3</a> </strong> from <strong><a href="//www.slideshare.net/TakeshiHasegawa1" target="_blank">Takeshi HASEGAWA</a></strong> </div>
    <p>
      IkaLogの動作や認識に関する問題は<a href="https://github.com/hasegaw/IkaLog/wiki/ja_FAQ">IkaLog FAQ</a>をご覧ください。
    </p>
    
    <h2>
      Q: もっと細かくデータを分析したい
    </h2>
    <p>
      A: stat.ink への機能追加にご協力ください。または、IkaLog から Fluentd に出力する、JSON 出力する等して分析してください。
    </p>

    <h2>
      Q: stat.inkの統計情報は何を表していますか
    </h2>
    <p>
      A: わかりません。
    </p>
    <p>
      例えば<a href="{{url route="entire/kd-win"}}">キル・デスと勝率の関係をまとめたもの</a>のナワバリバトルでは「4回死んでたら勝率が低い」ようなことは数字として見えますが、これが「死ななければ勝てる」のか「死なない状況を作れれば勝てる」のか「死なない状況を作ってくれる味方に巡り合えれば勝てる」のかはわかりませんし分析もしていません。いうまでもないですが、このページで「100%」となっているからといってそのK/Dにすれば勝てるというものでもありません。
    </p>

    <h2>
      Q: stat.inkの統計情報は偏っていますか
    </h2>
    <p>
      A: はい。
    </p>
    <p>
      stat.inkの全体統計は現時点では完全にIkaLogに頼っています（自動化されたもののみを利用するためイカレコの情報は使われません）。<br>
      IkaLogは導入するコスト、手間がかかりますし、そもそもstat.inkにデータを投げようと考える時点で確実に「ガチ勢」「上手い・強い側」に偏ります。<br>
      ナワバリバトルは（厳密な仕様は不明ですが）ガチ部屋あるいは殺し合い部屋に偏りますし、ガチマッチはA+からS+基準のマッチングに偏ります。<br>
      （このサイトを作った人間(A-～A)がウデマエ最下層というレベルで偏っています）
    </p>

    <h2>
      Q: 全体統計の対象はどのようになっていますか
    </h2>
    <p>
      A: 対象により異なりますが、
    </p>
    <ul>
      <li>
        自動化された記録であること（現実にはIkaLogからの投稿であること）
      </li>
      <li>
        データが明らかに欠けていないこと（例えばルールが不明、キルデス数が不明など）
      </li>
    </ul>
    <p>
      などが対象になっています。
    </p>
    <p>
      登録者の対戦相手を統計対象にしている場合もあります。（プレーヤー自身を含まず、チームメンバーの登録も制限するなど）
    </p>

    <h2>
      Q: <a href="{{url route="entire/weapons"}}">ブキ統計</a>の統計対象について
    </h2>
    <p>
      ブキ統計は、機械的に判定できる範囲で同じプレーヤーを含まないようになっています。
    </p>
    <ul>
      <li>
        登録者（プレーヤー）自身のブキは当然ほぼ連続して使用されるため登録者自身は集計除外
      </li>
      <li>
        プライベートマッチは「お遊び」の可能性が高いため集計除外
      </li>
      <li>
        タッグマッチの場合、登録者の味方チームは連続して同じブキを使用する可能性が高いため集計除外
      </li>
    </ul>
    <p>
      次のような場合は判定できないので（高度な技術を使えばなんとかならないこともないですが）通常通り使用されます。
    </p>
    <ul>
      <li>
        レギュラーマッチのフレンド合流。フレンドもデータを登録している場合（全部で <var>n</var> 人利用者がいるとします）、登録者自身は(<var>n</var> - 1)ずつ、その他のプレーヤは <var>n</var> ずつデータが登録されてしまいます。<br>
        例えば<a href="https://stat.ink/u/fetus_hina/142879">#142879</a>と<a href="https://stat.ink/u/okan/142880">#142880</a>は同じバトルですが、「ひな」「ひょうがらのおかん」は1つずつ、その他のデータは2つずつ登録されてしまっています。
      </li>
      <li>
        レギュラーマッチ・野良ガチマッチの連戦の場合でほかのプレーヤが部屋に残った場合、そのプレーヤは次のバトルでも記録されますので「ほんの少し」カウントしすぎる状況が発生します。
      </li>
    </ul>
    <p>
      これらの理由により、勝率の合計は50%になりませんし、真のデータより数えすぎているケースが存在するため厳密には正しい統計にはなりません。
    </p>
    <p>
      仮にこれらが完全にどうにかできるとしても、stat.inkの利用者はSplatoonプレーヤ全体から見ると確実に「上手な方」に偏っているため真の値にはなりません。
    </p>

    <h2>
      Q: 対応アプリを開発したい
    </h2>
    <p>
      A: <a href="https://github.com/fetus-hina/stat.ink/blob/master/API.md">APIを公開しています</a>。ご自由に開発をお願いします。許可とかは要りませんが、"agent" が既存のものとかぶらないようにしてください。<br>
      （エラー系の情報や仕様の記述がいろいろ足りていません。すみません。）
    </p>

    <h2>
      Q: 作者に還元したい
    </h2>
    <p>
      A: IkaLog 作者 hasegaw さんへ還元したい場合は <a href="http://d.ballade.jp/2015/10/IkaLogStore.html">IkaLog Store</a> 経由で何か購入してください（リンクを踏んだあと別の商品を購入してもアフィリエイトは反映されます。
    </p>
    <p>
      stat.ink の作者へ還元したい場合は <a href="http://www.amazon.co.jp/registry/wishlist/328GZTLVNILB3">何かください</a>。
    </p>
    <p>
      商品購入以外の方法として、バグを修正するとか、みんなに役立つ機能を実装するとかもあります。
    </p>
    <p>
      ※どちらのプロダクトも、商品を購入したからといって、バグを直してもらえる権利を得るとかそういうことはありません。ご理解の上でご購入ください。
    </p>

    <h2>
      Q: なんでこんなの作ったの
    </h2>
    <p>
      A: 自分で記録とるのめんどくさいじゃないですか。
    </p>
    <p>
      <a href="http://gigazine.net/news/20150804-splatoon-result-raspberry-pi-opencv/">ラズパイとカメラでキャプチャして勝敗を記録するツールについての記事</a>をまず見ていて、「これは導入できそうにないなあ」と思っていたところ、IkaLogを知って「このデータを活用すれば統計情報とか出せるね」となり、「でもIkaLogにはそういう部分ないのね」ってことで「じゃあ作りますか」と。
    </p>
    <p>
      「作るのは面倒じゃないのか」と言われると「プログラマは楽をするために全力を尽くす生き物」です。
      少なくとも私には全部手入力とかExcelで管理とか無理です。
      stat.inkについてる機能もいくつか「面倒でこんなの誰も使わないって」と思いながらつけたものがあります。
      実際にはそこそこ使われてたりして「マメだなあ」とか思ってます。
    </p>
  </div>
{{/strip}}
{{registerCss}}
  .container img {
    width:100%;
    max-width:530px;
    height: auto;
  }
{{/registerCss}}
