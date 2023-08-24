<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Map3;
use app\models\Special3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<int, Special3> $specials
 * @var array<int, array{
 *   special_id: int,
 *   count: int,
 *   cleared: int,
 *   avg_waves: int|float|numeric-string
 *   max_golden: int
 *   total_golden: int
 *   avg_golden: int|float|numeric-string
 *   max_power: int
 *   total_power: int
 *   avg_power: int|float|numeric-string
 *   total_rescues: int
 *   avg_rescues: int|float|numeric-string
 *   total_rescued: int
 *   avg_rescued: int|float|numeric-string
 *   total_defeat_boss: int
 *   avg_defeat_boss: int|float|numeric-string
 * }> $specialStats
 */

if (!$specialStats) {
  return;
}

$fmt = Yii::$app->formatter;

?>
<h3><?= Html::encode(Yii::t('app', 'Specials')) ?></h3>
<div class="table-responsive">
  <table class="table table-bordered table-condensed table-striped">
    <thead>
      <tr>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Special')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Jobs')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Avg. Waves')) ?></th>
        <th class="text-center"><?= Icon::goldenEgg() ?></th>
        <th class="text-center"><?= Icon::powerEgg() ?></th>
        <?= Html::tag('th', Html::encode(Yii::t('app-salmon3', 'R')), [
          'class' => 'auto-tooltip text-center',
          'title' => Yii::t('app-salmon3', 'Rescues'),
        ]) . "\n" ?>
        <?= Html::tag('th', Html::encode(Yii::t('app-salmon3', 'S')), [
          'class' => 'auto-tooltip text-center',
          'title' => Yii::t('app-salmon3', 'Rescued'),
        ]) . "\n" ?>
        <?= Html::tag('th', Html::encode(Yii::t('app-salmon3', 'Boss')), [
          'class' => 'auto-tooltip text-center',
          'title' => Yii::t('app-salmon3', 'Boss Salmonids'),
        ]) . "\n" ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($specials as $specialId => $special) { ?>
<?php $row = $specialStats[$specialId] ?? null; ?>
<?php if ($row) { ?>
      <tr>
        <th scope="row">
          <?= Icon::s3Special($special) . "\n" ?>
          <?= Html::encode(Yii::t('app-special3', $special->name)) . "\n" ?>
        </th>
        <td class="text-center"><?= $fmt->asInteger($row['count']) ?></th>
        <td class="text-center"><?= $fmt->asPercent($row['cleared'] / $row['count'], 1) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($row['avg_waves'], 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($row['avg_golden'], 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($row['avg_power'], 0) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($row['avg_rescues'], 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($row['avg_rescued'], 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($row['avg_defeat_boss'], 2) ?></td>
      </tr>
<?php } ?>
<?php } ?>
    </tbody>
  </table>
</div>
