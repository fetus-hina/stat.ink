<?php

declare(strict_types=1);

use app\assets\AboutAsset;
use app\assets\AppLinkAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Budoux;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$title = implode(' | ', [
    Yii::$app->name,
    Yii::t('app', 'Getting Started'),
]);
$this->context->layout = 'main';

$aboutAsset = AboutAsset::register($this);
$iconAsset = AppLinkAsset::register($this);
?>
<div class="container">
  <?php Budoux::begin(['lang' => 'ja-JP']); echo "\n" ?>
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
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
        <h3>
          1-a. イカリング 3 のデータを利用する
        </h3>
        <p>
          イカリング 3 (Nintendo Switch Online)の非公開APIを利用することで、データを取得するアプリが複数あります。
        </p>
        <h4>
          <?= Html::a(
            Html::encode('s3s'),
            'https://github.com/frozenpandaman/s3s',
            ['target' => '_blank'],
          ) . "\n"?>
        </h4>
        <p>
          s3sはPCまたはサーバ上で動作するアプリケーションです。
        </p>
        <p>
          Python 3の実行環境が必要です。
        </p>
        <p>
          設定方法については、次のページ等を参考にしてください。
        </p>
        <ul>
          <li>
            <?= Html::a(
              Html::encode('スプラトゥーン3でもstat.inkへバトルを記録！「s3s」導入手順'),
              'https://vanillasalt.net/2022/10/10/how-to-use-s3s/',
              ['target' => '_blank'],
            ) . "\n" ?>
          </li>
          <li>
            <?= Html::a(
              Html::encode('【Splatoon3】s3sを立てて戦績をstat.inkに自動アップする'),
              'https://zenn.dev/hibikine/articles/1febb4eb03b604',
              ['target' => '_blank'],
            ). "\n" ?>
          </li>
        </ul>
        <hr>
        <h4>
          <?= Html::a(
            Html::encode('s3si.ts'),
            'https://github.com/spacemeowx2/s3si.ts',
            ['target' => '_blank'],
          ) . "\n" ?>
        </h4>
        <p>
          s3si.tsはPCまたはサーバ上で動作するアプリケーションです。
        </p>
        <p>
          TypeScriptの実行環境(Deno)が必要です。
        </p>
      </div>

      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
        <h3>
          1-b. イカリング2のデータを利用する
        </h3>
        <p>
          イカリング2(Nintendo Switch Online)の非公開APIを利用することで、データを取得するアプリが複数あります。
        </p>
        <h4>
          <?= Html::a(
            Html::encode('splatnet2statink'),
            'https://github.com/frozenpandaman/splatnet2statink',
            ['target' => '_blank'],
          ) . "\n" ?>
        </h4>
        <p>
          splatnet2statinkはPCまたはサーバ上で動作するアプリケーションです。
        </p>
        <p>
          Python 3の実行環境が必要なので、一般の人にはあまりおすすめできませんが、プログラマなどであればこちらの方が取扱いやすいと思われます。
          （定期的な自動実行と組み合わせるなど）
        </p>
        <hr>
        <h4>
          <?= Html::a(
            implode('', [
              $iconAsset->squidTracks,
              Html::encode('SquidTracks'),
            ]),
            'https://github.com/hymm/squid-tracks',
            ['target' => '_blank'],
          ) . "\n" ?>
        </h4>
        <p>
          SquidTracksはPC上で動作するアプリケーションです。
        </p>
        <p>
          一部のブキやステージに対応していないため、新規のご利用はお控え下さい。
        </p>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
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
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 mb-3">
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
      このサイトは、相沢陽菜 &lt;hina@fetus.jp&gt; (<?= Icon::twitter() ?> fetus_hina, <?= Icon::github() ?> fetus-hina) が個人的に作成したものです。
      任天堂株式会社とは一切関係はありません。
      任天堂株式会社へこのサイトやIkaLogのことを問い合わせたりはしないでください。単純に迷惑になります。
    </p>
    <p>
      このサイトのソースコードはMIT Licenseに基づくオープンソースソフトウェアとして公開しています。
      MIT Licenseの範囲内で誰でも自由に改造・改良・フォークを行うことができます。
    </p>
  <?php Budoux::end(); echo "\n" ?>
</div>
