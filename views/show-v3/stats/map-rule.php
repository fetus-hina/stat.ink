<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Battle3FilterWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\Battle3FilterForm;
use app\models\Map3;
use app\models\Rule3;
use app\models\User;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Battle3FilterForm $filter
 * @var User $user
 * @var View $this
 * @var array<string, Map3> $maps
 * @var array<string, Rule3> $rules
 * @var array<string, array<string, array>> $mapStats
 * @var array<string, array> $totalStats
 */

$permLink = Url::to(
  array_merge(
    $filter->toPermLink(),
    ['show-v3/stats-map-rule', 'screen_name' => $user->screen_name],
  ),
  true,
);

$title = Yii::t('app', "{name}'s Battle Stats (by Mode and Stage)", [
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

  <?= Html::tag(
    'p',
    Html::a(
      implode(' ', [
        Icon::filter(),
        Html::encode(Yii::t('app', 'Filter')),
        Icon::scrollTo(),
      ]),
      '#filter-form',
      [
        'class' => [
          'btn',
          'btn-info',
          'btn-sm',
        ],
      ],
    ),
    ['class' => 'visible-xs-block mb-3'],
  ) . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 mb-3">
      <div class="table-responsive table-responsive-force">
        <?= $this->render('map-rule/table', compact('mapStats', 'maps', 'rules', 'totalStats', 'user')) . "\n" ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Battle3FilterWidget::widget([
        'route' => 'show-v3/stats-map-rule',
        'user' => $user,
        'filter' => $filter,
        'action' => 'summarize',

        'connectivity' => false,
        'knockout' => false,
        'lobby' => true,
        'map' => false,
        'rank' => false,
        'result' => false,
        'rule' => false,
        'term' => true,
        'weapon' => true,
      ]) . "\n" ?>
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
