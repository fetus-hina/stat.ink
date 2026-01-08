<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\show\v3\stats\SeasonXPowerAction;
use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\Rule3;
use app\models\Season3;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @phpstan-import-type DailyData from SeasonXPowerAction
 *
 * @var DailyData[] $dailyData
 * @var Rule3[] $rules
 * @var Season3 $season,
 * @var Season3[] $seasons,
 * @var User $user
 * @var View $this
 */

$permLink = Url::to(
  ['show-v3/stats-season-x-power', 'screen_name' => $user->screen_name, 'season' => $season->id],
  true,
);

$title = Yii::t('app', "{name}'s X Power", [
  'name' => $user->name,
]);

$this->title = implode(' | ', [Yii::$app->name, $title]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);

OgpHelper::profileV3($this, $user, $permLink, description: $title);

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 mb-3">
      <div class="mb-3">
        <?= trim(
          $this->render('season-x-power/season-selector', [
            'seasons' => $seasons,
            'season' => $season,
            'user' => $user,
          ]),
        ) . "\n" ?>
        <?= Html::tag(
          'h2',
          Html::encode(Yii::t('app-season3', $season->name)),
          ['class' => 'mt-0 mb-3'],
        ) . "\n" ?>
      </div>
<?php if ($dailyData) { ?>
      <div class="mb-3">
        <p class="m-0 p-0 small text-muted">
          <?= Html::encode(
            Yii::t('app', 'Regardless of your time zone setting, it is grouped using UTC.'),
          ) . "\n" ?>
        </p>
      </div>
      <div class="mb-3">
        <?= $this->render('season-x-power/daily-chart', [
          'dailyData' => $dailyData,
          'rules' => $rules,
          'season' => $season,
        ]) . "\n" ?>
      </div>
      <div class="mb-3">
        <?= $this->render('season-x-power/daily-table', [
          'dailyData' => $dailyData,
          'rules' => $rules,
          'season' => $season,
          'user' => $user,
        ]) . "\n" ?>
      </div>
<?php } else { ?>
      <p class="mb-3">
        <?= Html::encode(Yii::t('app', 'There are no data.')) . "\n" ?>
      </p>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
