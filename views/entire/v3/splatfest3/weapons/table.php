<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\BattleSummaryDialogAsset;
use app\assets\TableResponsiveForceAsset;
use app\models\Splatfest3;
use app\models\Splatfest3StatsWeapon;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Splatfest3 $splatfest
 * @var Splatfest3StatsWeapon[] $models
 * @var View $this
 */

$samples = array_sum(
  array_map(
    fn (Splatfest3StatsWeapon $model): int => $model->battles,
    $models,
  ),
);

$maxUseRate = $samples > 0
  ? max(
    array_map(
      fn (Splatfest3StatsWeapon $model): float => $model->battles / $samples,
      $models,
    ),
  )
  : 0.0;

$maxWinRate = min(
  0.75,
  max(
    array_filter(
      array_map(
        function (Splatfest3StatsWeapon $model): ?float {
          $wins = $model->wins;
          $battles = $model->battles;
          return $battles > 0 ? $wins / $battles : null;
        },
        $models,
      ),
      fn (?float $v): bool => $v !== null,
    ),
  ),
);

$cacheId = [__FILE__, $splatfest->id, $samples, Yii::$app->language];

BattleSummaryDialogAsset::register($this);
SortableTableAsset::register($this);
TableResponsiveForceAsset::register($this);

?>
<div class="mb-3">
  <p class="mb-1">
    <?= Html::encode(
      vsprintf('%s: %s', [
        Yii::t('app', 'Samples'),
        Yii::$app->formatter->asInteger($samples),
      ]),
    ) . "\n" ?>
  </p>
  <?= Yii::$app->cache->getOrSet(
    $cacheId,
    function () use ($models, $maxUseRate, $maxWinRate, $samples): string {
      return GridView::widget([
        'dataProvider' => Yii::createObject([
          'class' => ArrayDataProvider::class,
          'allModels' => $models,
          'pagination' => false,
          'sort' => false,
        ]),
        'columns' => require __DIR__ . '/table/columns-weapon.php',
        'emptyCell' => '',
        'emptyText' => '',
        'layout' => '{items}',
        'options' => ['class' => 'grid-view mb-2 table-responsive table-responsive-force'],
        'tableOptions' => ['class' => 'mb-0 table table-condensed table-hover table-sortable table-striped'],
      ]);
    },
    3600,
  ) . "\n" ?>
  <?= Html::tag(
    'p',
    Html::encode('*: p < 0.05 / **: p < 0.01'),
    ['class' => ['mb-2', 'small', 'text-muted', 'text-right']],
  ) . "\n" ?>
</div>
