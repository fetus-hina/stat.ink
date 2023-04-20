<?php

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\models\BigrunOfficialResult3;
use app\models\EggstraWorkOfficialResult3;
use app\models\StatBigrunDistribAbstract3;
use app\models\StatEggstraWorkDistribAbstract3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official
 * @var NormalDistribution|null $ruleOfThumbDistrib
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
            $official ?? $ruleOfThumbDistrib,
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
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class => Yii::t('app', 'Official Results'),
            StatBigrunDistribAbstract3::class, StatEggstraWorkDistribAbstract3::class => Yii::$app->name,
            NormalDistribution::class => Yii::t('app', 'Empirical Estimates'),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Users'),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class, NormalDistribution::class => '-',
            StatBigrunDistribAbstract3::class, StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->users),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Average'),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class => '-',
            NormalDistribution::class => '(' . $fmt->asDecimal($model->mean(), 2) . ')',
            StatBigrunDistribAbstract3::class => $fmt->asDecimal($model->average, 2),
            StatEggstraWorkDistribAbstract3::class => $fmt->asDecimal($model->average, 2),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Std Dev'),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class => '-',
            NormalDistribution::class => '(' . $fmt->asDecimal(sqrt($model->variance()), 2) . ')',
            StatBigrunDistribAbstract3::class => $fmt->asDecimal($model->stddev, 2),
            StatEggstraWorkDistribAbstract3::class => $fmt->asDecimal($model->stddev, 2),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 5]),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialResult3::class => $fmt->asInteger($model->gold),
            EggstraWorkOfficialResult3::class => $fmt->asInteger($model->gold),
            NormalDistribution::class => '(' . $fmt->asInteger($model->inverse(0.95)) . ')',
            StatBigrunDistribAbstract3::class => $fmt->asInteger($model->top_5_pct),
            StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->top_5_pct),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 20]),
          'value' => fn (?object $model): string => match ($model::class) {
            BigrunOfficialResult3::class => $fmt->asInteger($model->silver),
            EggstraWorkOfficialResult3::class => $fmt->asInteger($model->silver),
            NormalDistribution::class => '(' . $fmt->asInteger($model->inverse(0.80)) . ')',
            StatBigrunDistribAbstract3::class => $fmt->asInteger($model->top_20_pct),
            StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->top_20_pct),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Top {percentile}%', ['percentile' => 50]),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialResult3::class => $fmt->asInteger($model->bronze),
            EggstraWorkOfficialResult3::class => $fmt->asInteger($model->bronze),
            NormalDistribution::class => '(' . $fmt->asInteger($model->inverse(0.50)) . ')',
            StatBigrunDistribAbstract3::class => $fmt->asInteger($model->median),
            StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->median),
            default => throw new LogicException(),
          },
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
