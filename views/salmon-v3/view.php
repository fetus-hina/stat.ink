<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SalmonUserInfo3;
use app\components\widgets\SnsWidget;
use app\components\widgets\v3\BattlePrevNext;
use app\models\Salmon3;
use app\models\SalmonWave3;
use yii\helpers\ArrayHelper;
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
  ['salmon-v3/view',
    'screen_name' => $user->screen_name,
    'battle' => $model->uuid,
  ],
  true,
);

$isS3ImgGenAvailable = ArrayHelper::getValue(Yii::$app->params, 'useS3ImgGen');

$this->title = sprintf('%s | %s', Yii::$app->name, $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $canonicalUrl]);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $canonicalUrl]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
if ($user->twitter != '') {
  $this->registerMetaTag([
    'name' => 'twitter:creator',
    'content' => sprintf('@%s', $user->twitter),
  ]);
}
if ($isS3ImgGenAvailable) {
  $twitterCardImage = vsprintf('https://s3-img-gen.stats.ink/salmon/%s/%s.jpg', [
    rawurlencode(Yii::$app->language),
    rawurlencode($model->uuid),
  ]);
  $this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary_large_image']);
  $this->registerMetaTag(['name' => 'twitter:image', 'content' => $twitterCardImage]);
} else {
  $this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
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

$lang = Yii::$app->language;
$jsonUrl = str_starts_with($lang, 'en-') || str_starts_with($lang, 'ja-')
  ? ['api-v3/single-salmon', 'uuid' => $model->uuid]
  : ['api-v3/single-salmon', 'uuid' => $model->uuid, 'full' => 1];
$this->registerLinkTag([
  'rel' => 'alternate',
  'type' => 'application/json',
  'href' => Url::to(['api-v3/single-salmon', 'uuid' => $model->uuid, 'full' => 1], true),
]);

$waves = SalmonWave3::find()
  ->with([
    'event',
    'salmonSpecialUse3s',
    'salmonSpecialUse3s.special',
    'tide',
  ])
  ->andWhere(['salmon_id' => $model->id])
  ->orderBy([
    'salmon_id' => SORT_ASC,
    'wave' => SORT_ASC,
  ])
  ->cache(3600)
  ->all();

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
  <?= SnsWidget::widget([
    'jsonUrl' => $jsonUrl,
    'imageUrl' => $isS3ImgGenAvailable
      ? vsprintf('https://s3-img-gen.stats.ink/salmon/%s/%s.jpg', [
        rawurlencode(Yii::$app->language),
        rawurlencode($model->uuid),
      ])
      : null,
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= BattlePrevNext::widget([
        'user' => $user,
        'nextBattle' => $nextBattle,
        'prevBattle' => $prevBattle,
      ]) . "\n" ?>
      <?= $this->render('view/edit-button', ['model' => $model, 'user' => $user]) . "\n" ?>
      <?= $this->render('view/details', ['model' => $model]) . "\n" ?>
      <?= $this->render('view/waves', ['model' => $model, 'waves' => $waves]) . "\n" ?>
      <?= $this->render('view/players', ['model' => $model, 'waves' => $waves]) . "\n" ?>
      <?= $this->render('view/bosses', ['model' => $model]) . "\n" ?>
      <?= $this->render('view/edit-button', ['model' => $model, 'user' => $user]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= SalmonUserInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
