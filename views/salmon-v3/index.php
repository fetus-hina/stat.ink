<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Salmon3FilterWidget;
use app\components\widgets\SalmonUserInfo3;
use app\components\widgets\SnsWidget;
use app\models\Salmon3FilterForm;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon3FilterForm $filter
 * @var User $user
 * @var View $this
 * @var bool $spMode
 * @var string $permLink
 */

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

$lang = Yii::$app->language;
$jsonUrl = str_starts_with($lang, 'en-') || str_starts_with($lang, 'ja-')
  ? ['api-v3/user-salmon', 'screen_name' => $user->screen_name]
  : ['api-v3/user-salmon', 'screen_name' => $user->screen_name, 'full' => 1];

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
    'jsonUrl' => $jsonUrl,
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= $this->render($spMode ? 'index/sp' : 'index/pc', compact('user', 'dataProvider')) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Salmon3FilterWidget::widget(['filter' => $filter, 'user' => $user]) . "\n" ?>
      <?= SalmonUserInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
<?php if (!$spMode) { ?>
  <?= $this->render('index/pc/filter', compact('user')) . "\n" ?>
<?php } ?>
</div>
