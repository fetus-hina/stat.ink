<?php

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use app\components\widgets\Icon;
use app\models\Salmon3StatsPlayedWith;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3StatsPlayedWith[] $playerStats
 * @var View $this
 */

if (!$playerStats) {
  return;
}

$fmt = Yii::$app->formatter;

$jobsRequired = 5;

?>
<h3><?= Html::encode(Yii::t('app-salmon3', 'People You Played With')) ?></h3>
<div class="table-responsive">
  <table class="table table-bordered table-condensed table-striped mb-0">
    <thead>
      <tr>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app', 'Name')) ?></th>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon2', 'Jobs')) ?></th>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center" colspan="2"><?= Html::encode(Yii::t('app-salmon2', 'Hazard Level')) ?></th>
        <th class="text-center" colspan="2">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode(Yii::t('app-salmon3', 'Team Total')) . "\n" ?>
        </th>
        <th class="text-center" colspan="2">
          <?= Icon::goldenEgg() . "\n" ?>
          <?= Html::encode(Yii::t('app-salmon3', 'Personal')) . "\n" ?>
        </th>
        <th class="text-center" rowspan="2"><?=
          Html::tag('span', Html::encode(Yii::t('app-salmon3', 'R')), [
            'class' => 'auto-tooltip',
            'title' => Yii::t('app-salmon3', 'Rescues'),
          ])
        ?></th>
        <th class="text-center" rowspan="2"><?=
          Html::tag('span', Html::encode(Yii::t('app-salmon3', 'S')), [
            'class' => 'auto-tooltip',
            'title' => Yii::t('app-salmon3', 'Rescued'),
          ])
        ?></th>
      </tr>
      <tr>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Cleared')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Played')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Max.')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Avg.')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Max.')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Avg.')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($playerStats as $model) { ?>
      <tr>
        <th scope="row"><?=
          Html::tag(
            'span',
            Html::encode($model->name),
            [
              'class' => 'auto-tooltip',
              'title' => sprintf('%s #%s', $model->name, $model->number),
            ],
          )
        ?></th>
        <td class="text-center"><?= Html::encode($fmt->asInteger($model->jobs)) ?></td>
        <td class="text-center"><?= Html::encode(
          $fmt->asPercent($model->clear_jobs / $model->jobs, 1),
        ) ?></td>
        <td class="text-center"><?= Html::encode(
          $fmt->asDecimal($model->clear_waves / $model->jobs, 2),
        ) ?></td>
        <td class="text-center"><?= Html::encode(
          $model->max_danger_rate_cleared === null
            ? ''
            : $fmt->asPercent((int)$model->max_danger_rate_cleared / 100, 0),
        ) ?></td>
        <td class="text-center"><?= Html::encode(
          $model->max_danger_rate_played === null
            ? ''
            : $fmt->asPercent((int)$model->max_danger_rate_played / 100, 0),
        ) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asInteger($model->team_golden_egg_max)) ?>
        <?= Html::tag(
          'td',
          $model->team_golden_egg_avg !== null && $model->jobs >= $jobsRequired
            ? BattleSummaryItemWidget::widget([
              'battles' => $model->jobs,
              'total' => $model->jobs * $model->team_golden_egg_avg,
              'min' => $model->team_golden_egg_min,
              'max' => $model->team_golden_egg_max,
              'median' => $model->team_golden_egg_50,
              'q1' => $model->team_golden_egg_25,
              'q3' => $model->team_golden_egg_75,
              'pct5' => $model->team_golden_egg_05,
              'pct95' => $model->team_golden_egg_95,
              'stddev' => $model->team_golden_egg_sd,
              'tooltipText' => null,
              'summary' => sprintf('%s - %s', Yii::t('app-salmon2', 'Golden Eggs'), $model->name),
              'decimalLabel' => 1,
              'decimalValue' => 0,
            ])
            : $fmt->asDecimal($model->team_golden_egg_avg, 1),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <td class="text-center"><?= Html::encode($fmt->asInteger($model->golden_egg_max)) ?></td>
        <?= Html::tag(
          'td',
          $model->golden_egg_avg !== null && $model->jobs >= $jobsRequired
            ? BattleSummaryItemWidget::widget([
              'battles' => $model->jobs,
              'total' => $model->jobs * $model->golden_egg_avg,
              'min' => $model->golden_egg_min,
              'max' => $model->golden_egg_max,
              'median' => $model->golden_egg_50,
              'q1' => $model->golden_egg_25,
              'q3' => $model->golden_egg_75,
              'pct5' => $model->golden_egg_05,
              'pct95' => $model->golden_egg_95,
              'stddev' => $model->golden_egg_sd,
              'tooltipText' => null,
              'summary' => sprintf('%s - %s', Yii::t('app-salmon2', 'Golden Eggs'), $model->name),
              'decimalLabel' => 1,
              'decimalValue' => 0,
            ])
            : $fmt->asDecimal($model->golden_egg_avg, 1),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $model->rescue_avg !== null && $model->jobs >= $jobsRequired
            ? BattleSummaryItemWidget::widget([
              'battles' => $model->jobs,
              'total' => $model->jobs * $model->rescue_avg,
              'min' => $model->rescue_min,
              'max' => $model->rescue_max,
              'median' => $model->rescue_50,
              'q1' => $model->rescue_25,
              'q3' => $model->rescue_75,
              'pct5' => $model->rescue_05,
              'pct95' => $model->rescue_95,
              'stddev' => $model->rescue_sd,
              'tooltipText' => null,
              'summary' => sprintf('%s - %s', Yii::t('app-salmon3', 'Rescues'), $model->name),
              'decimalLabel' => 1,
              'decimalValue' => 0,
            ])
            : $fmt->asDecimal($model->rescue_avg, 1),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $model->rescued_avg !== null && $model->jobs >= $jobsRequired
            ? BattleSummaryItemWidget::widget([
              'battles' => $model->jobs,
              'total' => $model->jobs * $model->rescued_avg,
              'min' => $model->rescued_min,
              'max' => $model->rescued_max,
              'median' => $model->rescued_50,
              'q1' => $model->rescued_25,
              'q3' => $model->rescued_75,
              'pct5' => $model->rescued_05,
              'pct95' => $model->rescued_95,
              'stddev' => $model->rescued_sd,
              'tooltipText' => null,
              'summary' => sprintf('%s - %s', Yii::t('app-salmon3', 'Rescued'), $model->name),
              'decimalLabel' => 1,
              'decimalValue' => 0,
            ])
            : $fmt->asDecimal($model->rescued_avg, 1),
          ['class' => 'text-center'],
        ) . "\n" ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
