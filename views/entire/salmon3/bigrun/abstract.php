<?php

declare(strict_types=1);

use app\models\BigrunOfficialResult3;
use app\models\EggstraWorkOfficialResult3;
use app\models\StatBigrunDistribAbstract3;
use app\models\StatEggstraWorkDistribAbstract3;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official
 * @var StatBigrunDistribAbstract3|StatEggstraWorkDistribAbstract3|null $model
 * @var View $this
 */

if (!$model) {
  return;
}

$fmt = Yii::$app->formatter;

?>
<div class="mb-3">
  <div class="table-responsive">
    <?= GridView::widget([
      'dataProvider' => Yii::createObject([
        'class' => ArrayDataProvider::class,
        'allModels' => array_values(
          array_filter([
            $model,
            $official,
          ]),
        ),
        'pagination' => false,
        'sort' => false,
      ]),
      'emptyCell' => '-',
      'layout' => '{items}',
      'tableOptions' => ['class' => 'table table-bordered table-striped w-sm-auto m-0 nobr'],
      'columns' => [
        [
          'label' => '',
          'value' => fn (Model|null $model): string => match (true) {
            $model instanceof BigrunOfficialResult3 => Yii::t('app', 'Official Results'),
            $model instanceof EggstraWorkOfficialResult3 => Yii::t('app', 'Official Results'),
            $model instanceof StatBigrunDistribAbstract3 => Yii::$app->name,
            $model instanceof StatEggstraWorkDistribAbstract3 => Yii::$app->name,
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Users'),
          'value' => fn (Model|null $model): string => match (true) {
            $model instanceof BigrunOfficialResult3 => '-',
            $model instanceof EggstraWorkOfficialResult3 => '-',
            $model instanceof StatBigrunDistribAbstract3 => $fmt->asInteger($model->users),
            $model instanceof StatEggstraWorkDistribAbstract3 => $fmt->asInteger($model->users),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Average'),
          'value' => fn (Model|null $model): string => match (true) {
            $model instanceof BigrunOfficialResult3 => '-',
            $model instanceof EggstraWorkOfficialResult3 => '-',
            $model instanceof StatBigrunDistribAbstract3 => $fmt->asDecimal($model->average, 2),
            $model instanceof StatEggstraWorkDistribAbstract3 => $fmt->asDecimal($model->average, 2),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Std Dev'),
          'value' => fn (Model|null $model): string => match (true) {
            $model instanceof BigrunOfficialResult3 => '-',
            $model instanceof EggstraWorkOfficialResult3 => '-',
            $model instanceof StatBigrunDistribAbstract3 => $fmt->asDecimal($model->stddev, 2),
            $model instanceof StatEggstraWorkDistribAbstract3 => $fmt->asDecimal($model->stddev, 2),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 5]),
          'value' => fn (Model|null $model): string => match (true) {
            $model instanceof BigrunOfficialResult3 => $fmt->asInteger($model->gold),
            $model instanceof EggstraWorkOfficialResult3 => $fmt->asInteger($model->gold),
            $model instanceof StatBigrunDistribAbstract3 => $fmt->asInteger($model->top_5_pct),
            $model instanceof StatEggstraWorkDistribAbstract3 => $fmt->asInteger($model->top_5_pct),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 20]),
          'value' => fn (Model|null $model): string => match (true) {
            $model instanceof BigrunOfficialResult3 => $fmt->asInteger($model->silver),
            $model instanceof EggstraWorkOfficialResult3 => $fmt->asInteger($model->silver),
            $model instanceof StatBigrunDistribAbstract3 => $fmt->asInteger($model->top_20_pct),
            $model instanceof StatEggstraWorkDistribAbstract3 => $fmt->asInteger($model->top_20_pct),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 50]),
          'value' => fn (Model|null $model): string => match (true) {
            $model instanceof BigrunOfficialResult3 => $fmt->asInteger($model->bronze),
            $model instanceof EggstraWorkOfficialResult3 => $fmt->asInteger($model->bronze),
            $model instanceof StatBigrunDistribAbstract3 => $fmt->asInteger($model->median),
            $model instanceof StatEggstraWorkDistribAbstract3 => $fmt->asInteger($model->median),
            default => throw new LogicException(),
          },
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
