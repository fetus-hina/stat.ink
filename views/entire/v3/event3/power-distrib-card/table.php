<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Event3StatsPower;
use app\models\EventPeriod3;
use app\models\EventSchedule3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Event3StatsPower $abstract
 * @var EventPeriod3[] $periods
 * @var EventSchedule3 $schedule
 * @var View $this
 * @var array<int, Event3StatsPowerPeriod> $periodAbstracts
 */

$fmt = Yii::$app->formatter;

?>
<div class="table-responsive mb-3">
  <table class="table table-bordered table-striped table-condensed w-auto mb-0">
    <thead>
      <tr>
        <th></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Users')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Battles')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Average')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Std Dev')) ?></th>
        <th class="text-center">
          <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 75])) . "\n" ?>
        </th>
        <th class="text-center ">
          <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 50])) . "\n" ?>
        </th>
        <th class="text-center">
          <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 25])) . "\n" ?>
        </th>
        <th class="text-center">
          <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 20])) . "\n" ?>
        </th>
        <th class="text-center">
          <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 5])) . "\n" ?>
        </th>
        <th class="text-center text-muted">
          <?= Icon::statsHistogram() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'Bin Width')) . "\n" ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="text-center" scope="row"><?= Html::encode(Yii::t('app', 'Total')) ?></th>
        <td class="text-center"><?= Html::encode($fmt->asInteger($abstract->users)) ?></td>
        <td class="text-center">
          <?= Html::encode($fmt->asInteger($abstract->agg_battles)) . "\n" ?>
          <?= Html::tag(
            'span',
            vsprintf('(%s)', [
              Html::encode($fmt->asInteger($abstract->battles)),
            ]),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', 'Includes battles with unknown event power'),
            ],
          ) . "\n" ?>
        </td>
        <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->average, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($abstract->stddev, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($abstract->p25, 1)) ?></td>
        <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->p50, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($abstract->p75, 1)) ?></td>
        <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->p80, 1)) ?></td>
        <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->p95, 1)) ?></td>
        <td class="text-center text-muted">
          <?= Html::encode($fmt->asInteger($abstract->histogram_width)) . "\n" ?>
        </td>
      </tr>
<?php foreach ($periods as $i => $period) { ?>
      <tr class="text-muted">
        <th class="text-center" scope="row"><?= Html::encode(mb_chr(0x2460 + $i, 'UTF-8')) ?></th>
<?php if ($row = $periodAbstracts[$period->id] ?? null) { ?>
        <td class="text-center"><?= Html::encode($fmt->asInteger($row->users)) ?></td>
        <td class="text-center">
          <?= Html::encode($fmt->asInteger($row->agg_battles)) . "\n" ?>
          <?= Html::tag(
            'span',
            vsprintf('(%s)', [
              Html::encode($fmt->asInteger($row->battles)),
            ]),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', 'Includes battles with unknown event power'),
            ],
          ) . "\n" ?>
        </td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row->average, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row->stddev, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p25, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p50, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p75, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p80, 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p95, 1)) ?></td>
        <td class="text-center">-</td>
<?php } else { ?>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
        <td class="text-center">-</td>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
