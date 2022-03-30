<?php

use app\components\helpers\Html;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use yii\web\View;

/**
 * @var View $this
 */

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

  <h2>Splatoon 2</h2>
  <ul>
    <li>
      <span class="far fa-file-excel"></span>
      <a href="https://dl-stats.stat.ink/splatoon-2/battle-results-csv/">リザルト情報 (CSV)</a>
      <div style="margin-left:2em">
        ルール等や勝敗、ブキ構成、キルデスなどが一覧になっています。<br>
        「A1」は投稿者であり、このプレーヤーを統計に含めると大きく偏ることに注意してください。<br>
        <a href="https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/download-battle-csv.md">スキーマ Schema</a>
      </div>
    </li>
  </ul>

  <h2>Splatoon</h2>
  <ul>
    <li>
      <span class="far fa-file-excel"></span> ブキ・ルール・ステージ別にバトル数・勝率を集計したもの (CSV)
      <?= $this->render('_dl-langs', ['route' => 'download-stats/weapon-rule-map']) . "\n" ?>
    </li>
  </ul>
</div>
