<?php

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Splatfest;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var Splatfest $fest
 * @var View $this
 */

$title = "フェス「{$fest->name}」の各チーム勝率";
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
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= Html::encode(Yii::$app->name) ?>へ投稿されたデータからチームを推測し、勝率を計算したデータです。利用者の偏りから正確な勝率を表していません。
  </p>

<?php if ($fest->region->key === 'jp'): ?>
    <p>
      <a href="https://fest.ink/<?= Html::encode($fest->order) ?>">
        公式サイトから取得したデータを基に推測した勝率はイカフェスレートで確認できます。
      </a>
    </p>

<?php endif ?>
  <h2 id="rate">
    推定勝率: <span class="total-rate" data-team="alpha">取得中</span> VS <span class="total-rate" data-team="bravo">取得中</span>
  </h2>
  <p>
    <?= Html::encode($alpha->name) ?>チーム: <span class="total-rate" data-team="alpha">取得中</span>（サンプル数：<span class="sample-count" data-team="alpha">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-danger progress-bar-striped total-progressbar" style="width:0%" data-team="alpha">
    </div>
  </div>
  <p>
    <?= Html::encode($bravo->name) ?>チーム: <span class="total-rate" data-team="bravo">取得中</span>（サンプル数：<span class="sample-count" data-team="bravo">???</span>）
  </p>
  <div class="progress">
    <div class="progress-bar progress-bar-success progress-bar-striped total-progressbar" style="width:0%" data-team="bravo">
    </div>
  </div>
</div>
<?php
if ($alpha->color_hue !== null) {
  $this->registerCss(sprintf(
    '.progress-bar[data-team="alpha"]{background-color:hsl(%d,67%%,48%%)}',
    $alpha->color_hue
  ));
}
if ($bravo->color_hue !== null) {
  $this->registerCss(sprintf(
    '.progress-bar[data-team="bravo"]{background-color:hsl(%d,67%%,48%%)}',
    $bravo->color_hue
  ));
}
$this->registerJs(
  sprintf(
    'window.fest={start:new Date(%d*1000),end:new Date(%d*1000),data:%s};',
    strtotime($fest->start_at),
    strtotime($fest->end_at),
    Json::encode($results)
  ),
  View::POS_BEGIN
);
$this->registerJs(<<<'JS'
(function($, info) {
  var wins = {
    alpha: info.data.map(function(a){return a.alpha}).reduce(function(x,y){return x+y},0),
    bravo: info.data.map(function(a){return a.bravo}).reduce(function(x,y){return x+y},0),
    total: 0
  };
  wins.total = wins.alpha + wins.bravo;
  var wp = {
    alpha: wins.total > 0 ? wins.alpha * 100 / wins.total : Number.NaN,
    bravo: wins.total > 0 ? wins.bravo * 100 / wins.total : Number.NaN
  };

  $('.total-rate').each(function(){
    var v = wp[$(this).attr('data-team')];
    console.log(v);
    if (v === undefined || Number.isNaN(v)) {
      $(this).text('???');
    } else {
      $(this).text(v.toFixed(1) + '%');
    }
  });

  $('.sample-count').each(function(){
    var v = wins[$(this).attr('data-team')];
    if (v === undefined || Number.isNaN(v)) {
      $(this).text('???');
    } else {
      $(this).text(v);
    }
  });

  $('.total-progressbar').each(function(){
    var v = wp[$(this).attr('data-team')];
    if (v === undefined || Number.isNaN(v)) {
      $(this).css('width', 0);
    } else {
      $(this).css('width', v + '%');
    }
  });
})(jQuery, window.fest);
JS
);
