<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var list<array<string, mixed>> $groups
 * @var list<array<string, mixed>> $lobbies
 */

$route = 'show-v3/stats-monthly-report';
$month = $form->getFirstDayOfMonth();
$monthText = Yii::$app->formatter->asDate($month, Yii::t('app', 'MMMM y'));

$permLink = Url::to(
  [
    $route,
    'screen_name' => $user->screen_name,
    'year' => (int)$month->format('Y'),
    'month' => (int)$month->format('n'),
  ],
  true,
);

$title = Yii::t('app-show-v2', "{name}'s Monthly Report - {date}", [
  'name' => $user->name,
  'date' => $monthText,
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
  <?= Html::tag('h1', Yii::t('app-show-v2', "{name}'s Monthly Report - {date}", [
    'name' => Html::a(
      Html::encode($user->name),
      ['show-v3/user', 'screen_name' => $user->screen_name],
    ),
    'date' => Html::encode($monthText),
  ])) . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 mb-3">
      <?= $this->render('year-month/switch-report', [
        'form' => $form,
        'label' => Yii::t('app-user-mini-info', 'Daily Report'),
        'route' => 'show-v3/stats-daily-report',
        'user' => $user,
      ]) . "\n" ?>
      <?= $this->render('year-month/pager', [
        'form' => $form,
        'registerLinkTags' => true,
        'route' => $route,
        'user' => $user,
      ]) . "\n" ?>
      <?= $this->render('monthly-report/groups', compact('form', 'groups', 'user')) . "\n" ?>
<?php foreach ($lobbies as $lobbyInfo) { ?>
      <hr>
      <?= $this->render('monthly-report/lobby', [
        'form' => $form,
        'lobby' => $lobbyInfo['lobby'],
        'rules' => $lobbyInfo['rules'],
        'user' => $user,
      ]) . "\n" ?>
<?php } ?>
      <hr>
      <?= $this->render('year-month/pager', compact('form', 'route', 'user')) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
