<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Rule3;
use app\models\StatXPowerDistribAbstract3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<int, StatXPowerDistribAbstract3> $abstracts
 */

?>
<div class="mb-3">
  <?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => $rules,
      'pagination' => false,
      'sort' => false,
    ]),
    'emptyCell' => '-',
    'layout' => '{items}',
    'options' => ['class' => 'grid-view table-responsive'],
    'tableOptions' => ['class' => 'table table-striped table-bordered w-auto m-0'],
    'columns' => [
      [
        'label' => Yii::t('app', 'Mode'),
        'headerOptions' => ['class' => 'text-center'],
        'format' => 'raw',
        'value' => fn (Rule3 $model): string => Html::a(
          implode(' ', [
            Icon::s3Rule($model),
            Html::encode(Yii::t('app-rule3', $model->name)),
          ]),
          '#' . rawurlencode($model->key),
        ),
      ],
      [
        'encodeLabel' => false,
        'label' => implode(' ', [
          Icon::inkling(),
          Html::encode(Yii::t('app', 'Users')),
        ]),
        'headerOptions' => ['class' => 'text-center'],
        'format' => 'integer',
        'value' => fn (Rule3 $model): ?int => $abstracts[$model->id]?->users,
        'contentOptions' => ['class' => 'text-right'],
      ],
      [
        'label' => Yii::t('app', 'Average'),
        'headerOptions' => ['class' => 'text-center'],
        'format' => ['decimal', 1],
        'value' => fn (Rule3 $model): ?float => TypeHelper::floatOrNull(
          $abstracts[$model->id]?->average,
        ),
        'contentOptions' => ['class' => 'text-right fw-bold'],
      ],
      [
        'label' => Yii::t('app', 'Std Dev'),
        'headerOptions' => ['class' => 'text-center'],
        'format' => ['decimal', 1],
        'value' => fn (Rule3 $model): ?float => TypeHelper::floatOrNull(
          $abstracts[$model->id]?->stddev,
        ),
        'contentOptions' => ['class' => 'text-right'],
      ],
      [
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 5]),
        'headerOptions' => ['class' => 'text-center'],
        'format' => ['decimal', 1],
        'value' => fn (Rule3 $model): ?float => TypeHelper::floatOrNull(
          $abstracts[$model->id]?->pct95,
        ),
        'contentOptions' => ['class' => 'text-right fw-bold'],
      ],
      [
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 20]),
        'headerOptions' => ['class' => 'text-center'],
        'format' => ['decimal', 1],
        'value' => fn (Rule3 $model): ?float => TypeHelper::floatOrNull(
          $abstracts[$model->id]?->pct80,
        ),
        'contentOptions' => ['class' => 'text-right fw-bold'],
      ],
      [
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 25]),
        'headerOptions' => ['class' => 'text-center'],
        'format' => ['decimal', 1],
        'value' => fn (Rule3 $model): ?float => TypeHelper::floatOrNull(
          $abstracts[$model->id]?->pct75,
        ),
        'contentOptions' => ['class' => 'text-right'],
      ],
      [
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 50]),
        'headerOptions' => ['class' => 'text-center'],
        'format' => ['decimal', 1],
        'value' => fn (Rule3 $model): ?float => TypeHelper::floatOrNull(
          $abstracts[$model->id]?->median,
        ),
        'contentOptions' => ['class' => 'text-right fw-bold'],
      ],
      [
        'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 75]),
        'headerOptions' => ['class' => 'text-center'],
        'format' => ['decimal', 1],
        'value' => fn (Rule3 $model): ?float => TypeHelper::floatOrNull(
          $abstracts[$model->id]?->pct25,
        ),
        'contentOptions' => ['class' => 'text-right'],
      ],
    ],
  ]) . "\n" ?>
</div>
