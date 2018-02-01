<?php
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;

$this->context->layout = 'main';
$title = Yii::t('app', 'Downloads');

$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    Splatoon 2 用データはまだ準備がありません。<br>
    There is no data available for Splatoon 2.
  </p>

  <hr>

  <p>
    データファイルをその場で生成してからダウンロード処理が行われます。
    クリックしてからしばらく時間がかかりますが、連打しないでください。
  </p>

  <p>
    各言語や文字コードは、ブキやステージの名前のローカライズ部分にのみ影響します。（どれを落としても本質的な情報は同じです）
  </p>

  <p>
    ダウンロード後すぐに何かわかるデータではありません。
    表計算ソフト(Excel等)やプログラムを駆使して何かを解析することを前提としたデータです。
  </p>

  <ul>
    <li>
      <span class="far fa-file-excel"></span> ブキ・ルール・ステージ別にバトル数・勝率を集計したもの (CSV)
      <?= $this->render('_dl-langs', ['route' => 'download-stats/weapon-rule-map']) . "\n" ?>
    </li>
  </ul>
</div>
