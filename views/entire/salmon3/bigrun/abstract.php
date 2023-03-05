<?php

declare(strict_types=1);

use app\models\StatBigrunDistribAbstract3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatBigrunDistribAbstract3|null $model
 * @var View $this
 */

if (!$model) {
  return;
}

?>
<div class="mb-3">
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => [$model],
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
        'format' => ['decimal', 2],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Average'),
      ],
      [
        'attribute' => 'stddev',
        'contentOptions' => ['class' => 'text-right'],
        'format' => ['decimal', 2],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Std Dev'),
      ],
      [
        'attribute' => 'top_5_pct',
        'contentOptions' => ['class' => 'text-right'],
        'format' => 'integer',
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 5]),
      ],
      [
        'attribute' => 'top_20_pct',
        'contentOptions' => ['class' => 'text-right'],
        'format' => 'integer',
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 20]),
      ],
      [
        'attribute' => 'median',
        'contentOptions' => ['class' => 'text-right'],
        'format' => 'integer',
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 50]),
      ],
    ],
  ]) . "\n" ?>
</div>
