<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\LobbyGroup3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var LobbyGroup3|null $lobbyGroup
 * @var LobbyGroup3[] $lobbyGroups
 * @var Rule3 $rule
 * @var Rule3[] $rules
 * @var User $user
 * @var View $this
 * @var array<string, mixed> $data
 * @var bool $isRanked
 */

$title = Yii::t('app-show-v2', "{name}'s Battle Stats ({rule})", [
  'name' => $user->name,
  'rule' => Yii::t('app-rule3', $rule->name),
]);

$permLink = Url::to(
  array_filter(
    [
      'show-v3/stats-rule-trend',
      'screen_name' => $user->screen_name,
      'rule' => $rule->key,
      'lobby' => $lobbyGroup?->key,
    ],
    fn ($v): bool => $v !== null,
  ),
  true,
);

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
  <?= Html::tag('h1', Yii::t('app-show-v2', "{name}'s Battle Stats ({rule})", [
    'name' => Html::a(
      Html::encode($user->name),
      ['show-v3/user', 'screen_name' => $user->screen_name],
    ),
    'rule' => Html::encode(Yii::t('app-rule3', $rule->name)),
  ])) . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 mb-3">
      <?= $this->render('rule-trend/nav', compact('lobbyGroup', 'lobbyGroups', 'rule', 'rules', 'user')) . "\n" ?>
      <p class="small text-muted">
        <?= Html::encode(Yii::t('app', 'Excluded: Private Battles')) . "\n" ?>
      </p>
<?php if ($data['battles'] < 1) { ?>
      <p class="text-muted"><?= Html::encode(Yii::t('app', 'No Data')) ?></p>
<?php } else { ?>
      <?= $this->render('rule-trend/section', [
        'charts' => 'win-rate',
        'data' => $data,
        'id' => 'win-rate',
        'isRanked' => $isRanked,
        'title' => Yii::t('app-battle', 'Winning Percentage'),
      ]) . "\n" ?>
      <?= $this->render('rule-trend/section', [
        'charts' => 'stats',
        'data' => $data,
        'id' => 'stats',
        'isRanked' => $isRanked,
        'title' => Yii::t('app', 'Stats'),
      ]) . "\n" ?>
      <?= $this->render('rule-trend/section', [
        'charts' => 'inked',
        'data' => $data,
        'id' => 'inked',
        'isRanked' => $isRanked,
        'title' => Yii::t('app-results', 'Turf Inked'),
      ]) . "\n" ?>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
