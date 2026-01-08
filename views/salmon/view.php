<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SalmonUserInfo;
use app\components\widgets\SnsWidget;
use app\models\Salmon2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon2 $model
 * @var View $this
 */

$user = $model->user;

$title = Yii::t('app-salmon2', 'Results of {userName}\'s job', [
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
    <?= Yii::t('app-salmon2', 'Results of {userName}\'s job', [
      'userName' => Html::a(
        Html::encode($user->name),
        ['salmon/index', 'screen_name' => $user->screen_name]
      ),
    ]) . "\n" ?>
  </h1>
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= $this->render('view/_prev_next', [
        'user' => $user,
        'current' => $model,
        'prev' => $model->getPrevious(),
        'next' => $model->getNext(),
      ]) . "\n" ?>
      <?= $this->render('view/_edit', [
        'model' => $model,
      ]) . "\n" ?>
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
