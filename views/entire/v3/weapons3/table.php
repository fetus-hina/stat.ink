<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\models\StatWeapon3Usage;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatWeapon3Usage[] $data
 * @var View $this
 */

if (!$data) {
  echo Html::tag('p', Yii::t('app', 'No Data'));
  return;
}

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

$totalBattles = array_sum(
  array_map(
    fn (StatWeapon3Usage $row): int => $row->battles,
    $data,
  ),
);

$maxBattles = max(
  array_map(
    fn (StatWeapon3Usage $m): int => $m->battles,
    $data,
  ),
);

$maxWinRate = max(
  array_map(
    fn (StatWeapon3Usage $m): float => $m->wins / $m->battles,
    $data,
  ),
);

$dataProvider = Yii::createObject([
  'class' => ArrayDataProvider::class,
  'allModels' => $data,
  'pagination' => false,
  'sort' => false,
]);

?>
<div class="mb-3">
  <?= GridView::widget([
    'columns' => require __DIR__ . '/table/columns.php',
    'dataProvider' => $dataProvider,
    'emptyCell' => '',
    'emptyText' => '',
    'layout' => '{items}',
    'options' => ['class' => 'grid-view mb-2 table-responsive table-responsive-force'],
    'tableOptions' => ['class' => 'mb-0 table table-condensed table-hover table-sortable table-striped'],
  ]) . "\n" ?>
  <?= Html::tag(
    'p',
    Html::encode('*: p < 0.05 / **: p < 0.01'),
    ['class' => ['mb-2', 'small', 'text-muted', 'text-right']],
  ) . "\n" ?>
</div>

