<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\OgpHelper;
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

OgpHelper::profileV3($this, $user, $permLink);

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
