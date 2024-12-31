<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
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
        'allModels' => array_values(
          array_filter([
            $border,
            $model,
            $official,
            $ruleOfThumbDistrib,
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
            BigrunOfficialResult3::class => Yii::t('app', 'Official Results'),
            EggstraWorkOfficialResult3::class => Yii::t('app', 'Official Results'),
            NormalDistribution::class => Yii::t('app', 'Empirical Estimates'),
            StatBigrunDistribUserAbstract3::class => Yii::$app->name,
            StatEggstraWorkDistribUserAbstract3::class => Yii::$app->name,
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Users'),
          'format' => 'raw',
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class => Html::encode('-'),
            BigrunOfficialResult3::class => Html::encode('-'),
            EggstraWorkOfficialResult3::class => Html::encode('-'),
            NormalDistribution::class => Html::encode('-'),
            StatBigrunDistribUserAbstract3::class => implode(' ', [
              Icon::inkling(),
              Html::encode($fmt->asInteger($model->users)),
            ]),
            StatEggstraWorkDistribUserAbstract3::class => implode(' ', [
              Icon::inkling(),
              Html::encode($fmt->asInteger($model->users)),
            ]),
            default => throw new LogicException(),
          },
        ],
        [
          'contentOptions' => ['class' => 'text-center'],
          'headerOptions' => ['class' => 'text-center'],
          'label' => Yii::t('app', 'Average'),
          'format' => 'raw',
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class => $fmtEggs(null),
            BigrunOfficialResult3::class => $fmtEggs(null),
            EggstraWorkOfficialResult3::class => $fmtEggs(null),
            NormalDistribution::class => $fmtEggs($model->mean()),
            StatBigrunDistribUserAbstract3::class => $fmtEggs($model->average),
            StatEggstraWorkDistribUserAbstract3::class => $fmtEggs($model->average),
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
            StatEggstraWorkDistribUserAbstract3::class => $fmt->asDecimal($model->stddev, 2),
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
          'format' => 'raw',
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class => $fmtEggs($model->gold),
            BigrunOfficialResult3::class => $fmtEggs($model->gold),
            EggstraWorkOfficialResult3::class => $fmtEggs($model->gold),
            NormalDistribution::class => $fmtEggs((int)$model->inverse(0.95), true),
            StatBigrunDistribUserAbstract3::class => $fmtEggs($model->p95),
            StatEggstraWorkDistribUserAbstract3::class => $fmtEggs($model->p95),
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
          'format' => 'raw',
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class => $fmtEggs($model->silver),
            BigrunOfficialResult3::class => $fmtEggs($model->silver),
            EggstraWorkOfficialResult3::class => $fmtEggs($model->silver),
            NormalDistribution::class => $fmtEggs((int)$model->inverse(0.80), true),
            StatBigrunDistribUserAbstract3::class => $fmtEggs($model->p80),
            StatEggstraWorkDistribUserAbstract3::class => $fmtEggs($model->p80),
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
          'format' => 'raw',
          'value' => fn (object $model): string => match ($model::class) {
            BigrunOfficialBorder3::class => $fmtEggs($model->bronze),
            BigrunOfficialResult3::class => $fmtEggs($model->bronze),
            EggstraWorkOfficialResult3::class => $fmtEggs($model->bronze),
            NormalDistribution::class => $fmtEggs((int)$model->inverse(0.50), true),
            StatBigrunDistribUserAbstract3::class => $fmtEggs($model->p50),
            StatEggstraWorkDistribUserAbstract3::class => $fmtEggs($model->p50),
            default => throw new LogicException(),
          },
        ],
      ],
    ]) . "\n" ?>
  </div>
</div>
