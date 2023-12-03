<?php

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\models\BigrunOfficialBorder3;
use app\models\BigrunOfficialResult3;
use app\models\EggstraWorkOfficialResult3;
use app\models\StatBigrunDistribUserAbstract3;
use app\models\StatEggstraWorkDistribAbstract3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunOfficialBorder3|null $border
 * @var BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official
 * @var NormalDistribution|null $ruleOfThumbDistrib
 * @var StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $model
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
            $border,
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
            BigrunOfficialBorder3::class => Yii::t('app-salmon3', 'Official Thresholds'),
            BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class => Yii::t('app', 'Official Results'),
            NormalDistribution::class => Yii::t('app', 'Empirical Estimates'),
            StatBigrunDistribUserAbstract3::class, StatEggstraWorkDistribAbstract3::class => Yii::$app->name,
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Users'),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class, BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class, NormalDistribution::class => '-',
            StatBigrunDistribUserAbstract3::class, StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->users),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Average'),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class, BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class => '-',
            NormalDistribution::class => '(' . $fmt->asDecimal($model->mean(), 2) . ')',
            StatBigrunDistribUserAbstract3::class => $fmt->asDecimal($model->average, 2),
            StatEggstraWorkDistribAbstract3::class => $fmt->asDecimal($model->average, 2),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Std Dev'),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class, BigrunOfficialResult3::class, EggstraWorkOfficialResult3::class => '-',
            NormalDistribution::class => '(' . $fmt->asDecimal(sqrt($model->variance()), 2) . ')',
            StatBigrunDistribUserAbstract3::class => $fmt->asDecimal($model->stddev, 2),
            StatEggstraWorkDistribAbstract3::class => $fmt->asDecimal($model->stddev, 2),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'encodeLabel' => false,
          'label' => implode('<br>', [
            Html::encode(Yii::t('app-salmon-scale3', 'Gold')), // FIXME: category
            Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 5])),
          ]),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class, BigrunOfficialResult3::class => $fmt->asInteger($model->gold),
            EggstraWorkOfficialResult3::class => $fmt->asInteger($model->gold),
            NormalDistribution::class => '(' . $fmt->asInteger($model->inverse(0.95)) . ')',
            StatBigrunDistribUserAbstract3::class => $fmt->asInteger($model->p95),
            StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->top_5_pct),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'encodeLabel' => false,
          'label' => implode('<br>', [
            Html::encode(Yii::t('app-salmon-scale3', 'Silver')), // FIXME: category
            Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 20])),
          ]),
          'value' => fn (?object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class, BigrunOfficialResult3::class => $fmt->asInteger($model->silver),
            EggstraWorkOfficialResult3::class => $fmt->asInteger($model->silver),
            NormalDistribution::class => '(' . $fmt->asInteger($model->inverse(0.80)) . ')',
            StatBigrunDistribUserAbstract3::class => $fmt->asInteger($model->p80),
            StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->top_20_pct),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'encodeLabel' => false,
          'label' => implode('<br>', [
            Html::encode(Yii::t('app-salmon-scale3', 'Bronze')), // FIXME: category
            Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 50])),
          ]),
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class, BigrunOfficialResult3::class => $fmt->asInteger($model->bronze),
            EggstraWorkOfficialResult3::class => $fmt->asInteger($model->bronze),
            NormalDistribution::class => '(' . $fmt->asInteger($model->inverse(0.50)) . ')',
            StatBigrunDistribUserAbstract3::class => $fmt->asInteger($model->p50),
            StatEggstraWorkDistribAbstract3::class => $fmt->asInteger($model->median),
            default => throw new LogicException(),
          },
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
