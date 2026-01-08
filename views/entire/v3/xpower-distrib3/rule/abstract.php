<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\Icon;
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

?>
<div class="mb-3">
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
    'options' => ['class' => 'table-responsive'],
    'tableOptions' => ['class' => 'table table-bordered table-striped w-auto m-0'],
    'columns' => [
      [
        'attribute' => 'users',
        'contentOptions' => ['class' => 'text-right'],
        'format' => 'integer',
        'headerOptions' => ['class' => 'text-center'],
        'label' => implode(' ', [
          Icon::inkling(),
          Html::encode(Yii::t('app', 'Users')),
        ]),
        'encodeLabel' => false,
      ],
      [
        'attribute' => 'average',
        'contentOptions' => ['class' => 'text-right fw-bold'],
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
        'attribute' => 'pct95',
        'contentOptions' => ['class' => 'text-right fw-bold'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 5]),
      ],
      [
        'attribute' => 'pct80',
        'contentOptions' => ['class' => 'text-right fw-bold'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 20]),
      ],
      [
        'attribute' => 'pct75',
        'contentOptions' => ['class' => 'text-right'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 25]),
      ],
      [
        'attribute' => 'median',
        'contentOptions' => ['class' => 'text-right fw-bold'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 50]),
      ],
      [
        'attribute' => 'pct25',
        'contentOptions' => ['class' => 'text-right'],
        'format' => ['decimal', 1],
        'headerOptions' => ['class' => 'text-center'],
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 75]),
      ],
      [
        'attribute' => 'histogram_width',
        'contentOptions' => ['class' => 'text-right text-muted'],
        'format' => 'integer',
        'headerOptions' => ['class' => 'text-center text-muted'],
        'label' => implode(' ', [
          Icon::statsHistogram(),
          Html::encode(Yii::t('app', 'Bin Width')),
        ]),
        'encodeLabel' => false,
      ],
    ],
  ]) . "\n" ?>
</div>
