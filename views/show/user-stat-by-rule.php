<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\StatByRuleAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\WinLoseLegend;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', "{name}'s Battle Stats (by Mode)", ['name' => $user->name]);
$this->title = implode(' | ', [Yii::$app->name, $title]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

StatByRuleAsset::register($this);

$this->registerCss('.pie-flot-container{height:200px}');
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
      <?= WinLoseLegend::widget() . "\n" ?>
      <?= Html::tag('div', '', [
        'id' => 'stat',
        'data' => [
          'screen-name' => $user->screen_name,
          'json' => Json::encode($data),
          'no-data' => Yii::t('app', 'No Data'),
          'filter' => Json::encode($filter->toQueryParams()),
        ],
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= BattleFilterWidget::widget([
        'route' => 'show/user-stat-by-rule',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
        'rule' => false,
        'result' => false,
      ]) . "\n" ?>
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
