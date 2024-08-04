<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use app\models\StatWeapon3XUsagePerVersion;
use yii\base\Model;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[]|StatWeapon3XUsage[]|StatWeapon3XUsagePerVersion[] $data
 * @var View $this
 */

if (!$data) {
  echo Html::tag('p', Yii::t('app', 'No Data'));
  return;
}

$totalBattles = array_sum(
  array_map(
    fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $row): int => $row->battles,
    $data,
  ),
);

$maxBattles = max(
  array_map(
    fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $m): int => $m->battles,
    $data,
  ),
);

$maxWinRate = max(
  array_map(
    fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $m): float => $m->wins / $m->battles,
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
  <?= Tabs::widget([
    'items' => [
      [
        'active' => true,
        'label' => Yii::t('app', 'Detailed'),
        'content' => implode('', [
          GridView::widget([
            'columns' => require __DIR__ . '/table/columns.php',
            'dataProvider' => $dataProvider,
            'emptyCell' => '',
            'emptyText' => '',
            'filterModel' => Yii::createObject(Model::class), // dirty hack to use "filter row"
            'filterRowOptions' => ['class' => 'battle-row-group-header'],
            'layout' => '{items}',
            'options' => ['class' => 'grid-view mb-2 table-responsive table-responsive-force'],
            'tableOptions' => [
              'class' => 'mb-0 table table-condensed table-hover table-sortable table-striped',
            ],
          ]),
          Html::tag(
            'p',
            Html::encode('*: p < 0.05 / **: p < 0.01'),
            [
              'class' => ['mt-0', 'mb-3', 'small', 'text-muted', 'text-right'],
            ],
          ),
        ]),
      ],
      [
        'label' => Yii::t('app', 'Win %'),
        'content' => $this->render('table/win-rate', [
          'data' => $data,
        ]),
      ],
    ],
    'tabContentOptions' => [
      'class' => 'my-3 tab-content',
    ],
  ]) . "\n" ?>
</div>
