<?php

declare(strict_types=1);

use app\models\User;
use yii\web\View;

/**
 * @var DateTimeInterface $activityFrom
 * @var DateTimeInterface $activityTo
 * @var User $user
 * @var View $this
 * @var string $permLink
 * @var string $tab
 */

$title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);

$this->context->layout = 'main';
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->userIcon->absUrl ?? $user->jdenticonPngUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
}

?>
<div id="profile" class="container">
  <div class="row">
    <?= $this->render('//show-user/profile/sidebar', ['user' => $user]) . "\n" ?>
    <div class="col-xs-12 col-md-9">
      <?= $this->render('//show-user/profile/tabs', [
        'tab' => $tab,
        'user' => $user,
      ]) . "\n" ?>
      <div class="row">
        <div class="col-xs-12" id="activity">
          <?= $this->render('//show-user/profile/activity', ['user' => $user]) . "\n" ?>
        </div>
      </div>
    </div>
  </div>
</div>
