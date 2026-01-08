<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\AboutAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$title = implode(' | ', [
  Yii::$app->name,
  'AVT-C875の接続の仕方',
]);
$this->context->layout = 'main';
$this->title = $title;

$aboutAsset = AboutAsset::register($this);
$assetMgr = Yii::$app->assetManager;

$img = function (string $filename, int $width) use ($aboutAsset, $assetMgr): string {
  return Html::img(
    $assetMgr->getAssetUrl($aboutAsset, $filename),
    [
      'alt' => '',
      'title' => '',
      'style' => [
        'width' => '100%',
        'max-width' => "{$width}px",
      ]
    ]
  );
};
?>
<div class="container">
  <h1>
    AverMedia AVT-C875 "神うｐ" の接続の仕方
  </h1>
  <p>
    IkaLogを使用するためによく使われるキャプチャデバイス<a href="http://www.avermedia.co.jp/product_swap/avt-c875.html">「AVT-C875」（神うｐ）</a>の接続の仕方を説明します。
  </p>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2>
    物理的な接続
  </h2>
  <p>
    IkaLogをはじめて使おうという方はおそらくこういったシンプルな接続をされていると思います。
  </p>
  <p>
    <?= $img('kamiup1.png', 761) . "\n" ?>
  </p>
  <p>
    IkaLogは、このHDMIケーブルを流れている信号をPCに入力する（「横取りする」の方がわかりやすいかもしれません）ことでゲームのプレーを解析しましょう、というソフトです。
  </p>
  <p>
    ですから、どうにかしてこの信号を奪い取らないといけません。
  </p>
  <p>
    「このサイトについて」というページには、次のような図が書かれていますが、「神うｐ」は「キャプチャデバイス」であり、また、「HDMIスプリッター」が内蔵されていますので、少し接続の仕方が異なります。
  </p>
  <p>
    <?= $img('overview.ja.png', 685) . "\n" ?>
  </p>
  <p>
    次に実際の接続を示した図を出します。ぱっと見複雑ですが実はそうでもないので驚かないでください。
  </p>
  <p>
    <?= $img('kamiup2.png', 761) . "\n" ?>
  </p>
  <p>
    元々Wii Uとテレビをつないでいた（おそらく灰色の）HDMIケーブルを神うｐの「OUT」とテレビに付け替えて、Wii Uと神うｐの間には、神うｐ付属の短いHDMIケーブルを接続します。
  </p>
  <p>
    あとは、神うｐとPCをUSBで接続するだけです。（神うｐの電源はPCからUSBケーブルで取られますので、神うｐには３本のケーブルが生える形になります。ちなみに、ヘッドフォン端子とかは完全に無視してokです）
  </p>
  <p>
    どことどこをつないでいるのか確認しながらやればそんなに難しくないと思います。
  </p>

  <h2>PCの中の接続</h2>
  <p>
    まず、神うｐの接続をしてドライバーと公式アプリ（RECentral）インストールをしてください。
  </p>
  <p>
    Wii UとSplatoonを起動して、PCでRECentralを起動すればたぶんゲーム画面が映るはずです。
    （何か問題が起きた時の「切り分け」として、この「RECentralで映像は映るか」が重要になりますので、このアプリは入れておいてください。RECentralで映らないならIkaLog以前の問題なので、まず映るように設定等をがんばる必要があります）
  </p>
  <p>
    が、実はここから少しややこしい話が始まります。次の図を見てください。
  </p>
  <p>
    <?= $img('kamiup3.png', 761) . "\n" ?>
  </p>
  <p>
    ここに書いてあるのですが、IkaLogはそのままだと神うｐの映像を受け取れません（細かい話は省略します）。
    IkaLogを起動すると「C875」と表示されたりするのですが、これを選択しても動かないのです。
  </p>
  <p>
    そこで、図の下側に書いてあるように「LGP Stream Engine」というソフトをインストールします。
    このソフトは<a href="http://www.avermedia.co.jp/product_swap/avt-c875_download.html">AVT-C875のダウンロードページ</a>にあります。
    最新版だと「何か変」みたいなことになる場合があるので、そういった場合は微妙に古いものをインストールするなどしてみてください。
  </p>
  <p>
    Stream Engineは神うｐの映像信号を、IkaLog（その他のキャプチャデバイスからの信号を扱うソフト）が扱える形式に変換してくれます。
  </p>
  <p>
    IkaLogで設定するキャプチャデバイスは「AVT-C875」ではなく「LGP Stream Engine」になります。
  </p>

  <h2>Stream Engineの設定その他細かい話</h2>
  <p>
    <a href="http://mzsm.me/2015/09/23/hdmi-capture-avt-c875/">mzsmさんが「AVT-C875でスプラトゥーンをキャプチャしてみたメモ」という記事を書かれている</a>のでそちらを参照してみてください。
    ちょっと（かなり）IkaLogのバージョンが古いので設定画面が最新と違ったりしますが大体は同じです。
  </p>
  
  <hr>

  <h3>ライセンス情報</h3>
  <p>
    Wii U本体の背面画像は任天堂ホームページから引用しました。&#32;
    <a href="https://www.nintendo.co.jp/wiiu/hardware/parts/index.html">https://www.nintendo.co.jp/wiiu/hardware/parts/index.html</a>&#32;
    - &copy;Nintendo.
  </p>
  <p>
    AVT-C875本体の画像は製品紹介ページのギャラリーから引用しました。&#32;
    <a href="http://www.avermedia.co.jp/product_swap/avt-c875_gallery.html">http://www.avermedia.co.jp/product_swap/avt-c875_gallery.html</a>&#32;
    - &copy;AVerMedia Technologies Inc.
  </p>
  <p>
    テレビとして使用している画像は次のリソースを引用しました。&#32;
    <a href="http://www.publicdomainpictures.net/view-image.php?image=62615" rel="nofollow">http://www.publicdomainpictures.net/view-image.php?image=62615</a>
    - Public Domain
  </p>
  <p>
    PCとして使用している画像は次のリソースを引用しました。&#32;
    <a href="https://commons.wikimedia.org/wiki/File:Desktop-PC.svg" rel="nofollow">https://commons.wikimedia.org/wiki/File:Desktop-PC.svg</a>&#32;
    <a href="http://www.clker.com/clipart-23850.html" rel="nofollow">http://www.clker.com/clipart-23850.html</a>&#32;
    - Public Domain
  </p>
</div>
