<?php

/**
 * @copyright Copyright (C) 2021-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Battle Stats (Splatfest)", ['name' => $user->name]);
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
<?= $this->render('//show-v2/splatfest/splatfest-index', [
  'input' => $input,
  'region' => $region,
  'regions' => $regions,
  'splatfests' => $splatfests,
  'user' => $user,
]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= UserMiniInfo2::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
