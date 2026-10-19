<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\components\widgets\Icon;
use app\models\BigrunOfficialBorder3;
use app\models\BigrunOfficialResult3;
use app\models\EggstraWorkOfficialResult3;
use app\models\StatBigrunDistribUserAbstract3;
use app\models\StatEggstraWorkDistribUserAbstract3;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunOfficialBorder3|null $border
 * @var BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official
 * @var NormalDistribution|null $ruleOfThumbDistrib
 * @var StatBigrunDistribUserAbstract3|StatEggstraWorkDistribUserAbstract3|null $model
 * @var View $this
 */

if (!$model) {
  return;
}

$fmt = Yii::$app->formatter;

$fmtEggs = fn (int|float|null $value, bool $estimated = false): string => $value === null
  ? Html::encode('-')
  : vsprintf('%3$s%1$s %2$s%4$s', [
    Icon::goldenEgg(),
    Html::encode(
      match (true) {
        is_float($value) => $fmt->asDecimal($value, 2),
        is_int($value) => $fmt->asInteger($value),
      },
    ),
    Html::encode($estimated ? '(' : ''),
    Html::encode($estimated ? ')' : ''),
  ]);

?>
<div class="mb-3">
  <div class="table-responsive">
    <?= GridView::widget([
      'dataProvider' => Yii::createObject([
        'class' => ArrayDataProvider::class,
        'allModels' => [
          $border,
          $model,
          $official,
          $ruleOfThumbDistrib,
        ]
          |> array_filter(...)
          |> array_values(...),
        'pagination' => false,
        'sort' => false,
      ]),
      'emptyCell' => '-',
      'layout' => '{items}',
      'tableOptions' => ['class' => 'table table-bordered table-striped w-sm-auto m-0 nobr'],
      'columns' => [
        [
          'label' => '',
          'value' => fn (object $model): string => match (true) {
            $model instanceof BigrunOfficialBorder3 => Yii::t('app-salmon3', 'Official Thresholds'),
            $model instanceof BigrunOfficialResult3 => Yii::t('app', 'Official Results'),
            $model instanceof EggstraWorkOfficialResult3 => Yii::t('app', 'Official Results'),
            $model instanceof NormalDistribution => Yii::t('app-entire', 'Empirical Estimates'),
            $model instanceof StatBigrunDistribUserAbstract3 => Yii::$app->name,
            $model instanceof StatEggstraWorkDistribUserAbstract3 => Yii::$app->name,
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Users'),
          'format' => 'raw',
          'value' => fn (object $model): string => match (true) {
            $model instanceof BigrunOfficialBorder3 => Html::encode('-'),
            $model instanceof BigrunOfficialResult3 => Html::encode('-'),
            $model instanceof EggstraWorkOfficialResult3 => Html::encode('-'),
            $model instanceof NormalDistribution => Html::encode('-'),
            $model instanceof StatBigrunDistribUserAbstract3 => implode(' ', [
              Icon::inkling(),
              Html::encode($fmt->asInteger($model->users)),
            ]),
            $model instanceof StatEggstraWorkDistribUserAbstract3 => implode(' ', [
              Icon::inkling(),
              Html::encode($fmt->asInteger($model->users)),
            ]),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app-statistics', 'Average'),
          'format' => 'raw',
          'value' => fn (object $model): string => match (true) {
            $model instanceof BigrunOfficialBorder3 => $fmtEggs(null),
            $model instanceof BigrunOfficialResult3 => $fmtEggs(null),
            $model instanceof EggstraWorkOfficialResult3 => $fmtEggs(null),
            $model instanceof NormalDistribution => $fmtEggs($model->mean()),
            $model instanceof StatBigrunDistribUserAbstract3 => $fmtEggs($model->average),
            $model instanceof StatEggstraWorkDistribUserAbstract3 => $fmtEggs($model->average),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app-statistics', 'Std Dev'),
          'value' => fn (object $model): string => match (true) {
            $model instanceof BigrunOfficialBorder3, $model instanceof BigrunOfficialResult3, $model instanceof EggstraWorkOfficialResult3 => '-',
            $model instanceof NormalDistribution => '(' . $fmt->asDecimal(sqrt($model->variance()), 2) . ')',
            $model instanceof StatBigrunDistribUserAbstract3 => $fmt->asDecimal($model->stddev, 2),
            $model instanceof StatEggstraWorkDistribUserAbstract3 => $fmt->asDecimal($model->stddev, 2),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'encodeLabel' => false,
          'label' => implode('<br>', [
            Html::encode(Yii::t('app-salmon-scale3', 'Gold')), // FIXME: category
            Html::encode(Yii::t('app-statistics', 'Top {percentile}%', ['percentile' => 5])),
          ]),
          'format' => 'raw',
          'value' => fn (object $model): string => match (true) {
            $model instanceof BigrunOfficialBorder3 => $fmtEggs($model->gold),
            $model instanceof BigrunOfficialResult3 => $fmtEggs($model->gold),
            $model instanceof EggstraWorkOfficialResult3 => $fmtEggs($model->gold),
            $model instanceof NormalDistribution => $fmtEggs((int)$model->inverse(0.95), true),
            $model instanceof StatBigrunDistribUserAbstract3 => $fmtEggs($model->p95),
            $model instanceof StatEggstraWorkDistribUserAbstract3 => $fmtEggs($model->p95),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'encodeLabel' => false,
          'label' => implode('<br>', [
            Html::encode(Yii::t('app-salmon-scale3', 'Silver')), // FIXME: category
            Html::encode(Yii::t('app-statistics', 'Top {percentile}%', ['percentile' => 20])),
          ]),
          'format' => 'raw',
          'value' => fn (object $model): string => match (true) {
            $model instanceof BigrunOfficialBorder3 => $fmtEggs($model->silver),
            $model instanceof BigrunOfficialResult3 => $fmtEggs($model->silver),
            $model instanceof EggstraWorkOfficialResult3 => $fmtEggs($model->silver),
            $model instanceof NormalDistribution => $fmtEggs((int)$model->inverse(0.80), true),
            $model instanceof StatBigrunDistribUserAbstract3 => $fmtEggs($model->p80),
            $model instanceof StatEggstraWorkDistribUserAbstract3 => $fmtEggs($model->p80),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'encodeLabel' => false,
          'label' => implode('<br>', [
            Html::encode(Yii::t('app-salmon-scale3', 'Bronze')), // FIXME: category
            Html::encode(Yii::t('app-statistics', 'Top {percentile}%', ['percentile' => 50])),
          ]),
          'format' => 'raw',
          'value' => fn (object $model): string => match (true) {
            $model instanceof BigrunOfficialBorder3 => $fmtEggs($model->bronze),
            $model instanceof BigrunOfficialResult3 => $fmtEggs($model->bronze),
            $model instanceof EggstraWorkOfficialResult3 => $fmtEggs($model->bronze),
            $model instanceof NormalDistribution => $fmtEggs((int)$model->inverse(0.50), true),
            $model instanceof StatBigrunDistribUserAbstract3 => $fmtEggs($model->p50),
            $model instanceof StatEggstraWorkDistribUserAbstract3 => $fmtEggs($model->p50),
            default => throw new LogicException(),
          },
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
