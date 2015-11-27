{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'FAQ'|translate:'app'}}"}}

  {{\app\assets\AboutAsset::register($this)|@void}}
  {{$aboutAsset = $app->assetManager->getBundle('app\assets\AboutAsset')}}

  <div class="container">
    <h1>
      {{'FAQ'|translate:'app'|escape}}
    </h1>

    <div style="margin-bottom:15px">
      {{include file="@app/views/includes/ad.tpl"}}
    </div>

    <h2>
      Q: <a href="https://github.com/hasegaw/IkaLog/blob/master/doc/IkaUI.md">IkaLog</a> との関係は
    </h2>
    <p>
      A: stat.ink へのデータ送信対応ソフトの一つが IkaLog です。
    </p>
    <p>
      開発の経緯や実際の協力関係を除いて純粋に技術的関係だけを考えると、stat.ink へのデータ送信に対応しているソフトの一つが IkaLog である、ということになります。（IkaLog から見ると、IkaLog が対応している多数の出力先のうちの一つが stat.ink である、となります）
    </p>
    <p>
      実際には IkaLog とは重要なパートナー関係にありますが、それでも別の開発者が別々に開発する別々のプロダクトです。
    </p>
    <img src="{{$app->assetmanager->getAssetUrl($aboutAsset, 'ecosystem.png')|escape}}" alt="" title="">

    <h2>
      Q: キャプチャデバイスがないと使えないのですか
    </h2>
    <p>
      A: 現時点の現実的なシナリオでは「はい」
    </p>
    <p>
      現時点で、一般に広く公開されている対応ソフトウェアは IkaLog だけですので、現実的にはキャプチャデバイスが必要となります。
    </p>
    <p>
      stat.ink 自体の設計ポリシーとしては IkaLog 専用という事実はなく、対応するソフトウェアが用意されれば（作るのはあなたかもしれません）キャプチャ環境がなくても利用いただけるようになるでしょう。
    </p>

    <h2>
      Q. キャプチャデバイスを IkaLog が認識できません
    </h2>
    <p>
      A: IkaLog 側の問題（あるいは仕様）なので stat.ink で関知する範囲ではありませんが、スクリーンキャプチャ経由で動作したりするようですので、キャプチャデバイスの公式アプリで表示したものを取り込んだりできるかもしれません。
    </p>
    <div class="hidden-xs">
      <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/SAdkOp9vMUE?rel=0" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="visible-xs-block">
      <p>
        <a href="https://www.youtube.com/watch?v=SAdkOp9vMUE">IkaLog で ScreenCapture を使う (YouTube)</a>
      </p>
    </div>

    <h2>
      Q: キャプチャデバイスなしで利用できるサイトはありませんか
    </h2>
    <p>
      A: <a href="http://ika.lealog.net/">ウデマエアーカイブ</a>などが使えるかもしれません。手書きや Excel でも充分かもしれません。
    </p>

    <h2>
      Q: ウェブカメラでは代用できませんか
    </h2>
    <p>
      A: 現時点ではできません。
    </p>
    <p>
      IkaLog ではウェブカメラによる取り込みを試しているようです。IkaLog が対応すれば利用できるようになるはずですが、仕組み上認識精度は怪しいものになると思われます。
    </p>
    <p>
      また、IkaLog 以外に対応ソフトが作成されれば（作るのはあなたかもしれません）利用できるかもしれません。
    </p>

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
    
    <h2>
      Q: もっと細かくデータを分析したい
    </h2>
    <p>
      A: stat.ink への機能追加にご協力ください。または、IkaLog から Fluentd に出力する、JSON 出力する等して分析してください。
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
  </div>
{{/strip}}
{{registerCss}}
  .container img {
    width:100%;
    max-width:530px;
    height: auto;
  }
{{/registerCss}}
