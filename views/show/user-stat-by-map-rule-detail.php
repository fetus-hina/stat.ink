<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserDetailedStatsCell;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$this->context->layout = 'main';
$title = Yii::t('app', '{name}\'s Battle Stats (by Mode and Stage)', [
  'name' => $user->name,
]);
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

TableResponsiveForceAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <p><?= implode(' ', [
    Html::a(
      implode(' ', [
        Icon::back(),
        Html::encode(Yii::t('app', 'Back')),
      ]),
      ['show/user-stat-by-map-rule', 'screen_name' => $user->screen_name],
      ['class' => 'btn btn-default']
    ),
    Html::a(
      implode(' ', [
        Icon::search(),
        Html::encode(Yii::t('app', 'Search')),
      ]),
      '#filter-form',
      ['class' => [
        'visible-xs-inline',
        'btn',
        'btn-info',
      ]]
    ),
  ]) ?></p>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 table-responsive table-responsive-force">
      <table class="table table-condensed graph-container">
        <thead>
          <tr>
            <th></th>
<?php foreach ($ruleNames as $ruleKey => $ruleName) { ?>
            <th><?= Html::a(
              Html::encode($ruleName),
              ['show/user',
                'screen_name' => $user->screen_name,
                'filter' => [
                  'rule' => $ruleKey,
                ],
              ]
            ) ?></th>
<?php } ?>
          </tr>
        </thead>
        <tbody>
<?php foreach ($mapNames as $mapKey => $mapName) { ?>
          <tr>
            <th class="map-name"><?= Html::a(
              implode('', [
                Html::tag('span', Html::encode($mapName->name), [
                  'class' => 'visible-lg-inline',
                ]),
                Html::tag('span', Html::encode($mapName->short), [
                  'class' => 'hidden-lg',
                  'aria-hidden' => 'true',
                ]),
              ]),
              ['show/user',
                'screen_name' => $user->screen_name,
                'filter' => [
                  'map' => $mapKey,
                ],
              ]
            ) ?></th>
<?php foreach ($ruleNames as $ruleKey => $ruleName) { ?>
            <td class="detail-data"><?= UserDetailedStatsCell::widget([
              'rule' => $ruleKey,
              'data' => $data[$mapKey][$ruleKey],
            ]) ?></td>
<?php } ?>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= BattleFilterWidget::widget([
        'route' => 'show/user-stat-by-map-rule-detail',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
        'rule' => false,
        'map' => false,
        'result' => false,
      ]) . "\n" ?>
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
