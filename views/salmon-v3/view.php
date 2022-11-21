<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SalmonUserInfo;
use app\components\widgets\SnsWidget;
use app\components\widgets\v3\BattlePrevNext;
use app\models\Salmon3;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var Salmon3|null $nextBattle
 * @var Salmon3|null $prevBattle
 * @var View $this
 */

$user = $model->user;

$title = Yii::t('app-salmon2', 'Results of {userName}\'s job', [
  'userName' => $user->name,
]);

$canonicalUrl = Url::to(
  ['salmon-v3/view', 'screen_name' => $user->screen_name, 'id' => $model->uuid],
  true,
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

if ($prevBattle) {
  $this->registerLinkTag([
    'rel' => 'prev',
    'href' => Url::to(
      ['salmon-v3/view', 'screen_name' => $user->screen_name, 'battle' => $prevBattle->uuid],
      true,
    ),
  ]);
}

if ($nextBattle) {
  $this->registerLinkTag([
    'rel' => 'next',
    'href' => Url::to(
      ['salmon-v3/view', 'screen_name' => $user->screen_name, 'battle' => $nextBattle->uuid],
      true,
    ),
  ]);
}

?>
<div class="container">
  <h1>
    <?= Yii::t('app-salmon2', 'Results of {userName}\'s job', [
      'userName' => Html::a(
        Html::encode($user->name),
        ['salmon-v3/index', 'screen_name' => $user->screen_name]
      ),
    ]) . "\n" ?>
  </h1>
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= BattlePrevNext::widget([
        'user' => $user,
        'nextBattle' => $nextBattle,
        'prevBattle' => $prevBattle,
      ]) . "\n" ?>
      <?= $this->render('view/details', ['model' => $model]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= SalmonUserInfo::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
