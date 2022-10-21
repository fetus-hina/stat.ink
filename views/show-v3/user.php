<?php

declare(strict_types=1);

use app\assets\BattleListAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\Battle3FilterWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\User;
use yii\data\BaseDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var BaseDataProvider $battleDataProvider
 * @var Battle3FilterWidget $filter
 * @var User $user
 * @var View $this
 * @var array $summary
 */

$title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag([
  'name' => 'twitter:image',
  'content' => $user->iconUrl,
]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
}

BattleListAsset::register($this);

?>
<div class="container">
  <span itemscope itemtype="http://schema.org/BreadcrumbList">
    <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
      <?= Html::tag('meta', '', ['itemprop' => 'url', 'content' => Url::home(true)]) . "\n" ?>
      <?= Html::tag('meta', '', ['itemprop' => 'title', 'content' => Yii::$app->name]) . "\n" ?>
    </span>
  </span>
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= SnsWidget::widget([
    'tweetText' => (function () use ($title, $summary) {
      $fmt = Yii::$app->formatter;
      return sprintf(
        '%s [ %s ]',
        $title,
        Yii::t('app', 'Battles:{0} / Win %:{1} / Avg Kills:{2} / Avg Deaths:{3} / Kill Ratio:{4}', [
          $fmt->asInteger($summary->battle_count),
          $summary->wp === null ? '-' : $fmt->asPercent($summary->wp / 100, 1),
          $summary->kd_present > 0
            ? $fmt->asDecimal($summary->total_kill / $summary->kd_present, 2)
            : '-',
          $summary->kd_present > 0
            ? $fmt->asDecimal($summary->total_death / $summary->kd_present, 2)
            : '-',
          $summary->kd_present > 0
            ? ($summary->total_death == 0
              ? ($summary->total_kill == 0 ? '-' : '∞')
              : $fmt->asDecimal($summary->total_kill / $summary->total_death, 2)
            )
            : '-',
        ])
      );
    })(),
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= $this->render('user/index', [
        'battleDataProvider' => $battleDataProvider,
        'summary' => $summary,
        'user' => $user,
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Battle3FilterWidget::widget(['filter' => $filter, 'user' => $user]) . "\n" ?>
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12" id="table-config">
      <div>
        <label>
          <input type="checkbox" id="table-hscroll" value="1">
          <?= Html::encode(Yii::t('app', 'Always enable horizontal scroll')) . "\n" ?>
        <label>
      </div>
      <div class="row"><?= $this->render('user/filter') ?></div>
    </div>
  </div>
</div>
