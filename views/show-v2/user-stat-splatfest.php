<?php

declare(strict_types=1);

use app\components\helpers\Html;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo2;
use app\models\Region2;
use app\models\Splatfest2;
use app\models\User;
use yii\base\Model;
use yii\web\View;

/**
 * @var Model $input
 * @var Region2 $region
 * @var Region2[] $regions
 * @var Splatfest2[] $splatfests
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
