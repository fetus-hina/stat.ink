<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SalmonFilterWidget;
use app\components\widgets\SalmonUserInfo3;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$title = Yii::t('app-salmon2', "{name}'s Salmon Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

// $humanReadableSummary = $dataProvider->query->getHumanReadableSummary($user);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
// $this->registerMetaTag([
//   'name' => 'twitter:description',
//   'content' => $humanReadableSummary
//     ? $humanReadableSummary
//     : $title,
// ]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag([
  'name' => 'twitter:image',
  'content' => $user->iconUrl,
]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
}
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
    // 'tweetText' => $humanReadableSummary,
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <p class="text-right">
        <?= Html::a(
          implode(' ', [
            '<span class="fas fa-paint-roller"></span>',
            Yii::t('app', 'Battles'),
            '<span class="fas fa-fw fa-angle-right"></span>',
          ]),
          ['show-v3/user', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-default btn-xs']
        ) . "\n" ?>
      </p>
<?php if (true || $spMode) { ?>
      <?= $this->render('index/sp', compact('user', 'dataProvider')) . "\n" ?>
<?php } else { ?>
      <?= $this->render('index/_list_pc', compact('user', 'dataProvider')) . "\n" ?>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
<?php /* ?>
      <?= SalmonFilterWidget::widget([
        'user' => $user,
        'filter' => $filter,
      ]) . "\n" ?>
<?php */ ?>
      <?= SalmonUserInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
<?php if (!$spMode) { ?>
<?php /* ?>
  <?= $this->render('index/_config_pc', compact('user', 'dataProvider')) . "\n" ?>
<?php */ ?>
<?php } ?>
</div>
