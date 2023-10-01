<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3UserStatsGoldenEgg;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3UserStatsGoldenEgg $abstract
 * @var View $this
 * @var string $title
 */

if ($abstract->shifts < 10) {
  return;
}

$fmt = clone Yii::$app->formatter;
$fmt->nullDisplay = '-';

?>
<div class="col-12 col-xs-12 mb-3">
  <table class="table table-bordered table-striped mb-0">
    <thead>
      <tr>
        <th class="text-center">
          <?= ($abstract->map
            ? implode(' ', [
              Icon::s3SalmonStage($abstract->map ?? null),
              Html::encode(Yii::t('app-map3', $abstract->map?->short_name ?? null)),
            ])
            : '') . "\n"
          ?>
        </th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Team Total')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Personal')) ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th scope="row"><?= Html::encode(Yii::t('app-salmon2', 'Jobs')) ?></th>
        <td class="text-center">
          <?= Html::encode($fmt->asInteger($abstract->shifts)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Html::encode($fmt->asInteger($abstract->shifts)) . "\n" ?>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'Average')) . "\n" ?>
          (<?= Html::encode(Yii::t('app', 'Std Dev')) ?>)
        </th>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->average_team, 1)) . "\n" ?>
          (<?= Html::encode($fmt->asDecimal($abstract->stddev_team, 1)) ?>)
        </td>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->average_individual, 1)) . "\n" ?>
          (<?= Html::encode($fmt->asDecimal($abstract->stddev_individual, 1)) ?>)
        </td>
      </tr>
      <tr>
        <th scope="row"><?= Icon::goldenEgg() ?> <?= Html::encode(Yii::t('app', 'Minimum')) ?></th>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asInteger($abstract->min_team)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asInteger($abstract->min_individual)) . "\n" ?>
        </td>
      </tr>
      <tr>
        <th scope="row"><?= Icon::goldenEgg() ?> <?= Yii::t('app', 'Q<sub>1/4</sub>') ?></th>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->q1_team, 1)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->q1_individual, 1)) . "\n" ?>
        </td>
      </tr>
      <tr>
        <th scope="row"><?= Icon::goldenEgg() ?> <?= Html::encode(Yii::t('app', 'Median')) ?></th>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->q2_team, 1)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->q2_individual, 1)) . "\n" ?>
        </td>
      </tr>
      <tr>
        <th scope="row"><?= Icon::goldenEgg() ?> <?= Yii::t('app', 'Q<sub>3/4</sub>') ?></th>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->q3_team, 1)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asDecimal($abstract->q3_individual, 1)) . "\n" ?>
        </td>
      </tr>
      <tr>
        <th scope="row"><?= Icon::goldenEgg() ?> <?= Html::encode(Yii::t('app', 'Maximum')) ?></th>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asInteger($abstract->max_team)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asInteger($abstract->max_individual)) . "\n" ?>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode(
            Yii::t('app', 'Mode{translate_hint_stats}', [
              'translate_hint_stats' => '',
            ])
          ) . "\n" ?>
        </th>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asInteger($abstract->mode_team)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode($fmt->asInteger($abstract->mode_individual)) . "\n" ?>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?= Icon::statsHistogram() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'Bin Width')) . "\n" ?>
          <?= Html::tag(
            'span',
            Icon::help(),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t(
                'app',
                'The width of the histogram bins is automatically adjusted by Scott\'s rule-based algorithm.',
              ),
              'style' => [
                'cursor' => 'pointer',
              ],
            ],
          ) . "\n" ?>
        </th>
        <td class="text-center">
          <?= Html::encode($fmt->asInteger($abstract->histogram_width_team)) . "\n" ?>
        </td>
        <td class="text-center">
          <?= Html::encode($fmt->asInteger($abstract->histogram_width_individual)) . "\n" ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>
