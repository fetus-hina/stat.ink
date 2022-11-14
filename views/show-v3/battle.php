<?php

declare(strict_types=1);

use app\assets\BattleDetailAsset;
use app\components\formatters\v3\BattleSummaryFormatter;
use app\components\widgets\AdWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\components\widgets\v3\BattlePrevNext;
use app\models\Battle3;
use app\models\User;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Battle3 $model
 * @var Battle3|null $nextBattle
 * @var Battle3|null $prevBattle
 * @var View $this
 */

/** @var User $user */
$user = $model->user;

$title = Yii::t('app', 'Results of {name}\'s Battle', ['name' => $user->name]);
$canonicalUrl = Url::to(
  ['show-v3/battle',
    'screen_name' => $user->screen_name,
    'battle' => $model->uuid,
  ],
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
$this->registerMetaTag([
  'name' => 'twitter:description',
  'content' => BattleSummaryFormatter::format($model),
]);

if ($prevBattle) {
  $this->registerLinkTag([
    'rel' => 'prev',
    'href' => Url::to(
      ['show-v3/battle', 'screen_name' => $user->screen_name, 'battle' => $prevBattle->uuid],
      true
    ),
  ]);
}

if ($nextBattle) {
  $this->registerLinkTag([
    'rel' => 'next',
    'href' => Url::to(
      ['show-v3/battle', 'screen_name' => $user->screen_name, 'battle' => $nextBattle->uuid],
      true
    ),
  ]);
}

BattleDetailAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Yii::t('app', 'Results of {name}\'s Battle', [
      'name' => Html::a(
        Html::encode($user->name),
        ['show-v3/user', 'screen_name' => $user->screen_name]
      ),
    ]) . "\n" ?>
  </h1>
  <?= SnsWidget::widget() . "\n" ?>
<?php /*
  <?= $this->render('_battle_details_top_images', [
    'images' => [
      // $model->battleImageJudge,
      // $model->battleImageResult,
      // $model->battleImageGear,
    ],
  ]) . "\n" ?>
*/ ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= BattlePrevNext::widget([
        'user' => $user,
        'nextBattle' => $nextBattle,
        'prevBattle' => $prevBattle,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/embed-video', ['model' => $model]) . "\n" ?>
      <?= $this->render('//show-v3/battle/edit-button', ['model' => $model, 'user' => $user]) . "\n" ?>
      <?= $this->render('//show-v3/battle/details', ['model' => $model, 'user' => $user]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players', ['model' => $model]) . "\n" ?>
      <?= $this->render('//show-v3/battle/edit-button', ['model' => $model, 'user' => $user]) . "\n" ?>
      <?= BattlePrevNext::widget([
        'user' => $user,
        'nextBattle' => $nextBattle,
        'prevBattle' => $prevBattle,
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo3::widget([
        'activeLobby' => $model->lobby,
        'user' => $user,
      ]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
