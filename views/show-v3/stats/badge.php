<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\Rule3;
use app\models\SalmonKing3;
use app\models\Special3;
use app\models\TricolorRole3;
use app\models\User;
use app\models\UserBadge3KingSalmonid;
use app\models\UserBadge3Rule;
use app\models\UserBadge3Special;
use app\models\UserBadge3Tricolor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Rule3[] $rules
 * @var SalmonKing3[] $kings
 * @var Special3[] $specials
 * @var TricolorRole3[] $roles
 * @var User $user
 * @var View $this
 * @var array<string, UserBadge3KingSalmonid> $badgeKings
 * @var array<string, UserBadge3Rule> $badgeRules
 * @var array<string, UserBadge3Special> $badgeSpecials
 * @var array<string, UserBadge3Tricolor> $badgeTricolor
 */

$permLink = Url::to(['show-v3/stats-badge', 'screen_name' => $user->screen_name], true);
$title = Yii::t('app', "{name}'s Badge Progress", [
  'name' => $user->name,
]);

$this->title = implode(' | ', [Yii::$app->name, $title]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 mb-3">
      <p class="mb-3 text-muted small">
        <?= Html::encode(
          Yii::t('app', 'If there are any unsubmitted data, they have not been included in this tally.'),
        ) . "\n" ?>
      </p>
      <table class="mb-3 table table-bordered table-condensed table-striped">
        <thead>
          <tr>
            <th class="text-center" style="width:30px"></th>
            <th class="text-center omit" style="width:4em"></th>
            <th class="text-center omit"><?= Html::encode(Yii::t('app', 'Progress')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?= $this->render('badge/table/rules', compact('badgeRules', 'badgeTricolor', 'roles', 'rules')) . "\n" ?>
          <?= $this->render('badge/table/specials', compact('badgeSpecials', 'specials')) . "\n" ?>
          <?= $this->render('badge/table/salmon-kings', compact('badgeKings', 'kings')) . "\n" ?>
          <?= $this->render('badge/table/salmon-bosses', compact('badgeBosses', 'bosses')) . "\n" ?>
        </tbody>
      </table>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
