<?php
declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SalmonUserInfo;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$title = Yii::t('app-salmon2', "{name}'s Salmon Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

// $this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
// $this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
// $this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
// $this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
// $this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
// $this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
// $this->registerMetaTag([
//   'name' => 'twitter:image',
//   'content' => $user->iconUrl,
// ]);
// if ($user->twitter != '') {
//   $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
// }
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
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= $this->render('index/_list_pc', compact('user', 'dataProvider')) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= SalmonUserInfo::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
  <?= $this->render('index/_config_pc', compact('user', 'dataProvider')) . "\n" ?>
</div>
