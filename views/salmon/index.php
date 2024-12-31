<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SalmonFilterWidget;
use app\components\widgets\SalmonUserInfo;
use app\components\widgets\SnsWidget;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var ActiveDataProvider $dataProvider
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app-salmon2', "{name}'s Salmon Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$humanReadableSummary = $dataProvider->query->getHumanReadableSummary($user);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag([
  'name' => 'twitter:description',
  'content' => $humanReadableSummary
    ? $humanReadableSummary
    : $title,
]);
// $this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
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
    'feedUrl' => ['salmon/feed',
      'screen_name' => $user->screen_name,
      'lang' => preg_replace('/@.+$/', '',Yii::$app->language),
      'type' => 'rss',
    ],
    'tweetText' => $humanReadableSummary,
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
          ['show-v2/user', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-default btn-xs']
        ) . "\n" ?>
      </p>
<?php if ($spMode) { ?>
      <?= $this->render('index/_list_sp', compact('user', 'dataProvider')) . "\n" ?>
<?php } else { ?>
      <?= $this->render('index/_list_pc', compact('user', 'dataProvider')) . "\n" ?>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= SalmonFilterWidget::widget([
        'user' => $user,
        'filter' => $filter,
      ]) . "\n" ?>
      <?= SalmonUserInfo::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
<?php if (!$spMode) { ?>
  <?= $this->render('index/_config_pc', compact('user', 'dataProvider')) . "\n" ?>
<?php } ?>
</div>
