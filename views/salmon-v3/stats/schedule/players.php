<?php

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\PlayerTrait;
use app\components\widgets\Icon;
use app\models\SalmonEvent3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type PlayerStats from PlayerTrait
 *
 * @var PlayerStats[] $playerStats
 * @var View $this
 */

if (!$playerStats) {
  return;
}

$fmt = Yii::$app->formatter;

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
<?php foreach ($playerStats as $row) { ?>
      <tr>
        <th scope="row"><?=
          Html::tag(
            'span',
            Html::encode($row['name']),
            [
              'class' => 'auto-tooltip',
              'title' => sprintf('%s #%s', $row['name'], $row['number']),
            ],
          )
        ?></th>
        <td class="text-center"><?= Html::encode($fmt->asInteger($row['jobs'])) ?></td>
        <td class="text-center"><?=
          Html::encode($fmt->asPercent($row['cleared'] / $row['jobs'], 1))
        ?></td>
        <td class="text-center"><?=
          Html::encode($fmt->asDecimal($row['waves'] / $row['jobs'], 2))
        ?></td>
        <td class="text-center"><?=
          Html::encode(
            $row['clear_danger_rate'] === null
              ? ''
              : $fmt->asPercent((int)$row['clear_danger_rate'] / 100, 0),
          )
        ?></td>
        <td class="text-center"><?=
          Html::encode(
            $row['max_danger_rate'] === null
              ? ''
              : $fmt->asPercent((int)$row['max_danger_rate'] / 100, 0),
          )
        ?></td>
        <td class="text-center"><?= Html::encode($fmt->asInteger($row['max_golden_result'])) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row['avg_golden_result'], 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asInteger($row['max_golden'])) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row['avg_golden'], 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row['avg_rescue'], 1)) ?></td>
        <td class="text-center"><?= Html::encode($fmt->asDecimal($row['avg_rescued'], 1)) ?></td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
