<?php

declare(strict_types=1);

use app\actions\show\v3\stats\SeasonXPowerAction;
use app\assets\TableResponsiveForceAsset;
use app\models\Rule3;
use app\models\Season3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type DailyData from SeasonXPowerAction
 *
 * @var DailyData[] $dailyData
 * @var Rule3[] $rules
 * @var Season3 $season,
 * @var User $user
 * @var View $this
 */

TableResponsiveForceAsset::register($this);

?>
<div class="table-responsive table-responsive-force m-0 p-0">
  <?= Html::tag(
    'table',
    implode('', [
      $this->render('daily-table/thead', compact('rules', 'season', 'user')),
      Yii::$app->cache->getOrSet(
        [__FILE__, __LINE__, Yii::$app->locale, $user->id, $dailyData],
        fn () => $this->render('daily-table/tbody', [
          'dailyData' => $dailyData,
          'rules' => $rules,
          'season' => $season,
          'user' => $user,
        ]),
        3600,
      ),
    ]),
    [
      'class' => ['table', 'table-bordered', 'table-condensed', 'table-striped', 'm-0'],
    ],
  ) . "\n" ?>
</div>
