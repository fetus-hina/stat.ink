<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\models\StatXPowerDistribAbstract3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatXPowerDistribAbstract3|null $model
 * @var View $this
 */

if (!$model) {
  return;
}

TableResponsiveForceAsset::register($this);

?>
<div class="table table-responsive-force mb-3">
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => [$model],
      'key' => 'rule_id',
      'pagination' => false,
      'sort' => false,
    ]),
    'emptyCell' => '-',
    'layout' => '{items}',
    'tableOptions' => ['class' => 'table table-bordered table-striped w-auto m-0'],
    'columns' => [
      [
        'attribute' => 'users',
        'contentOptions' => ['class' => 'text-right'],
        'format' => 'integer',
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Users'),
      ],
      [
        'attribute' => 'average',
        'contentOptions' => ['class' => 'text-right'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Average'),
      ],
      [
        'attribute' => 'stddev',
        'contentOptions' => ['class' => 'text-right'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Std Dev'),
      ],
      [
        'attribute' => 'median',
        'contentOptions' => ['class' => 'text-right'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Median'),
      ],
    ],
  ]) . "\n" ?>
</div>
