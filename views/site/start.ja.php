<?php

declare(strict_types=1);

use app\assets\AboutAsset;
use app\assets\AppLinkAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->context->layout = 'main';

$title = implode(' | ', [
    Yii::$app->name,
    Yii::t('app', 'Getting Started'),
]);

$aboutAsset = AboutAsset::register($this);
$iconAsset = AppLinkAsset::register($this);
?>
<div class="container">
  <h1>
    このサイトについて
  </h1>
  <p>
    このサイトは、スプラトゥーンの勝敗データを収集して保存・解析するサイトです。
  </p>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2>
    ログを登録する方法
  </h2>
  <p>
    現在、stat.ink にデータを登録するには、主に3つの方法があります。
  </p>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <h3>
        1. イカリング2のデータを利用する (Splatoon 2)
      </h3>
      <p>
        イカリング2(Nintendo Switch Online)の非公開APIを利用することで、データを取得するアプリが複数あります。
      </p>
      <h4>
        <?= Html::a(
          implode('', [
            $iconAsset->getSquidTracks(),
            Html::encode('SquidTracks'),
          ]),
          'https://github.com/hymm/squid-tracks'
        ) . "\n" ?>
      </h4>
      <p>
        SquidTracksはPC上で動作するアプリケーションです。
      </p>
      <p>
        WindowsおよびMac用のものが配布されていて、すぐ利用できます。
        (Electron製アプリなので、Linuxなどでは環境を整えてビルドすれば利用できると思われます）
      </p>
      <p>
        Nintendo Switch Onlineへのログインが画面上で行えるので、お手軽です。
        起動時にアップデートの確認が行われるので、定期的に再起動して更新を適用してください。
      </p>

      <h4>
        <?= Html::a(
          Html::encode('splatnet2statink'),
          'https://github.com/frozenpandaman/splatnet2statink'
        ) . "\n" ?>
      </h4>
      <p>
        splatnet2statinkはPCまたはサーバ上で動作するアプリケーションです。
      </p>
      <p>
        Python 3の実行環境が必要なので、一般の人にはあまりおすすめできませんが、プログラマなどであればこちらの方が取扱いやすいと思われます。
        （定期的な自動実行と組み合わせるなど）
      </p>
      <p>
        こちらもNintendo Switch Onlineへのログインが行えます。
        また、起動時にアップデートの確認が行われるので、定期的に再起動して更新を適用してください。
        更新しない場合、新しいブキやギアが「不明」になったりします。
      </p>

      <h3>
        2. 画面を解析する (Splatoon 1)
      </h3>
      <p>
        ※現在、Splatoon 2 に対応したきちんと動作する IkaLog はリリースされていません。
      </p>
      <p>
        <a href="https://github.com/hasegaw/IkaLog/wiki/ja_WinIkaLog">IkaLog</a>などの対応ソフトを利用して、画面を解析して自動的に戦績を登録する方法です。
      </p>
      <p>
        IkaLogの場合、Switch/Wii Uからテレビへ出力している映像信号のコピーをPCに入力することでプレーデータを解析します。
        次のようなイメージです。
      </p>
      <?= Html::img(
        Yii::$app->assetManager->getAssetUrl($aboutAsset, 'overview.ja.png'),
        [
          'alt' => '',
          'title' => '',
          'style' => [
            'width' => '100%',
            'max-width' => '530px',
          ],
        ]
      ) . "\n" ?>
      <p>
        アプリケーションが動作していれば自動的・正確に多数のデータが<?= Html::encode(Yii::$app->name) ?>に送信されてきます。
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
        <?= Html::a(
          'IkaLogと組み合わせてよく使われるAVT-C875"神うｐ"の接続が（よく使われる割に特殊な接続になるので）わかりづらいと思われるので簡単に説明を書きました。',
          ['site/kamiup']
        ) . "\n" ?>
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
        <?= Html::encode(Yii::t('app-start', 'Manually')) . "\n" ?>
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
          <?= Html::a(
            Html::encode(Yii::$app->name) . 'へのユーザ登録',
            ['user/register']
          ) ?>を行ってください。
          ユーザ登録が既にお済みでしたらログインしてください。
        </li>
        <li>
          <?= Html::a(
            'プロフィールと設定',
            ['user/profile']
          ) ?>画面を開きます。
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
    <?= Html::encode(Yii::$app->name) ?>は<a href="https://github.com/fetus-hina/stat.ink/blob/master/API.md">APIを公開しています</a>ので、自作のソフトウェアによっても登録が行えます。
  </p>
  <p>
    もちろんあなた専用のアプリを作成しても構いませんし、広く公開すると喜ぶ人がいるかもしれません。
  </p>

  <hr>

  <p>
    このサイトは、相沢陽菜 &lt;hina@fetus.jp&gt; (<span class="fab fa-twitter left"></span>fetus_hina, <span class="fab fa-github left"></span>fetus-hina) が個人的に作成したものです。
    任天堂株式会社とは一切関係はありません。
    任天堂株式会社へこのサイトやIkaLogのことを問い合わせたりはしないでください。単純に迷惑になります。
  </p>
  <p>
    このサイトのソースコードはMIT Licenseに基づくオープンソースソフトウェアとして公開しています。
    MIT Licenseの範囲内で誰でも自由に改造・改良・フォークを行うことができます。
  </p>
</div>
