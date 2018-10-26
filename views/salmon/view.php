<?php
declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SalmonUserInfo;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$user = $model->user;

$title = Yii::t('app-salmon2', 'Results of {userName}\'s Work', [
    'userName' => $user->name,
]);
$canonicalUrl = Url::to(
    ['salmon/view', 'screen_name' => $user->screen_name, 'id' => $model->id],
    true
);

$this->title = sprintf('%s | %s', Yii::$app->name, $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $canonicalUrl]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'photo']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $canonicalUrl]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
if ($user->twitter != '') {
    $this->registerMetaTag([
        'name' => 'twitter:creator',
        'content' => sprintf('@%s', $user->twitter),
    ]);
}

?>
<div class="container">
  <h1>
    <?= Yii::t('app-salmon2', 'Results of {userName}\'s Work', [
      'userName' => Html::a(
        Html::encode($user->name),
        ['salmon/index', 'screen_name' => $user->screen_name]
      ),
    ]) . "\n" ?>
  </h1>
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= $this->render('view/_detail', [
        'model' => $model,
        'user' => $user,
      ]) . "\n" ?>
      <?= $this->render('view/_waves', ['model' => $model]) . "\n" ?>
      <?= $this->render('view/_users', ['model' => $model]) . "\n" ?>
      <?= $this->render('view/_boss', ['model' => $model]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= SalmonUserInfo::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
