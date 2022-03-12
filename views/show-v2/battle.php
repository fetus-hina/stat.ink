<?php

declare(strict_types=1);

use app\assets\BattleDetailAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use app\models\Battle2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Battle2 $battle
 * @var View $this
 */

$user = $battle->user;

// head-related {{{
$title = Yii::t('app', 'Results of {name}\'s Battle', ['name' => $user->name]);
$canonicalUrl = Url::to(
  ['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
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
$summary = [];
if ($battle->rule) {
  $summary[] = Yii::t('app-rule2', $battle->rule->name);
}
if ($battle->map) {
  $summary[] = Yii::t('app-map2', $battle->map->name);
}
if ($battle->is_win !== null) {
  $summary[] = ($battle->is_win)
    ? Yii::t('app', 'Won')
    : Yii::t('app', 'Lost');
}
$this->registerMetaTag([
  'name' => 'twitter:description',
  'content' => implode(' | ', $summary),
]);
if ($battle->previousBattle) {
  $this->registerLinkTag([
    'rel' => 'prev',
    'href' => Url::to(
      ['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->previousBattle->id],
      true
    ),
  ]);
}
if ($battle->nextBattle) {
  $this->registerLinkTag([
    'rel' => 'next',
    'href' => Url::to(
      ['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->nextBattle->id],
      true
    ),
  ]);
}
// }}}

BattleDetailAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Yii::t('app', 'Results of {name}\'s Battle', [
      'name' => Html::a(
        Html::encode($user->name),
        ['show-v2/user', 'screen_name' => $user->screen_name]
      ),
    ]) . "\n" ?>
  </h1>
  <?= SnsWidget::widget([
    'jsonUrl' => ['api-v2-battle/view',
        'id' => $battle->id,
        'format' => 'pretty',
    ],
  ]) . "\n" ?>
<?php /* トップ画像 {{{ */ ?>
  <?= $this->render('_battle_details_top_images', [
    'images' => [
      $battle->battleImageJudge,
      $battle->battleImageResult,
      $battle->battleImageGear,
    ],
  ]) . "\n" ?>
<?php /* }}} */ ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
<?php /* 前/次のバトルへのリンク {{{ */ ?>
<?php if ($battle->previousBattle || $battle->nextBattle): ?>
      <div class="row" style="margin-bottom:15px">
<?php if ($battle->previousBattle): ?>
        <div class="col-xs-6">
          <?= Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fa fa-fw fa-angle-double-left']),
              Yii::t('app', 'Prev. Battle'),
            ]),
            ['/show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->previousBattle->id],
            ['class' => 'btn btn-default']
          ) . "\n" ?>
        </div>
<?php endif; ?>
<?php if ($battle->nextBattle): ?>
        <div class="col-xs-6 pull-right text-right">
          <?= Html::a(
            implode('', [
              Yii::t('app', 'Next Battle'),
              Html::tag('span', '', ['class' => 'fa fa-fw fa-angle-double-right']),
            ]),
            ['/show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->nextBattle->id],
            ['class' => 'btn btn-default']
          ) . "\n" ?>
        </div>
<?php endif; ?>
      </div>
<?php endif; ?>
<?php /* }}} */ ?>
<?php /* 埋め込み動画 {{{ */ ?>
<?php if ($battle->link_url && EmbedVideo::isSupported($battle->link_url)): ?>
      <?= EmbedVideo::widget(['url' => $battle->link_url]) . "\n" ?>
<?php $this->registerCss('.video{margin-bottom:15px}'); ?>
<?php endif; ?>
<?php /* }}} */ ?>
<?php /* 編集 {{{ */ ?>
<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $user->id): ?> 
      <p class="text-right">
        <?= Html::a(
          Html::encode(Yii::t('app', 'Edit')),
          ['/show-v2/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
      </p>
<?php endif; ?>
<?php /* }}} */ ?>
      <?= $this->render('_battle_details', [
        'battle' => $battle,
      ]) . "\n" ?>
<?php if ($battle->getBattlePlayers()->exists()): ?>
      <?= $this->render('_battle_details_players', [
        'battle' => $battle,
      ]) . "\n" ?>
<?php endif; ?>
<?php /* 編集 {{{ */ ?>
<?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->id == $user->id): ?>
      <p class="text-right">
        <?= Html::a(
          Html::encode(Yii::t('app', 'Edit')),
          ['/show-v2/edit-battle', 'screen_name' => $user->screen_name, 'battle' => $battle->id],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
      </p>
<?php endif; ?>
<?php /* }}} */ ?>
<?php /* 前/次のバトルへのリンク {{{ */ ?>
<?php if ($battle->previousBattle || $battle->nextBattle): ?>
      <div class="row" style="margin-bottom:15px">
<?php if ($battle->previousBattle): ?>
        <div class="col-xs-6">
          <?= Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fa fa-fw fa-angle-double-left']),
              Yii::t('app', 'Prev. Battle'),
            ]),
            ['/show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->previousBattle->id],
            ['class' => 'btn btn-default']
          ) . "\n" ?>
        </div>
<?php endif; ?>
<?php if ($battle->nextBattle): ?>
        <div class="col-xs-6 pull-right text-right">
          <?= Html::a(
            implode('', [
              Yii::t('app', 'Next Battle'),
              Html::tag('span', '', ['class' => 'fa fa-fw fa-angle-double-right']),
            ]),
            ['/show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $battle->nextBattle->id],
            ['class' => 'btn btn-default']
          ) . "\n" ?>
        </div>
<?php endif; ?>
      </div>
<?php endif; ?>
<?php /* }}} */ ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo2::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
