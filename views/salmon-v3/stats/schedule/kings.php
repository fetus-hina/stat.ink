<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Map3;
use app\models\SalmonKing3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<int, SalmonKing3> $kings
 * @var array<int, array{
 *    king_id: int,
 *    appearances: int,
 *    defeated: int,
 *    defeated_by_me: int,
 *    gold_scale: ?int,
 *    silver_scale: ?int,
 *    bronze_scale: ?int
 * }> $kingStats
 */

if (!$kingStats) {
  return;
}

$fmt = Yii::$app->formatter;

$renderScale = fn (string $icon, int $number): string => Html::tag(
  'span',
  sprintf('%s %s', $icon, $fmt->asInteger($number)),
  ['class' => 'nobr mr-3'],
);

?>
<h3><?= Html::encode(Yii::t('app-salmon3', 'King Salmonids')) ?></h3>
<div class="table-responsive">
  <table class="table table-bordered table-condensed table-striped">
    <thead>
      <tr>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon3', 'King Salmonid')) ?></th>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon3', 'Appearances')) ?></th>
        <th class="text-center" colspan="2"><?= Html::encode(Yii::t('app-salmon3', 'Defeated')) ?></th>
        <th class="text-center" colspan="4"><?= Html::encode(Yii::t('app-salmon3', 'Scales')) ?></th>
      </tr>
      <tr>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeated')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeat %')) ?></th>
        <th class="text-center"><?= Icon::goldScale() ?></th>
        <th class="text-center"><?= Icon::silverScale() ?></th>
        <th class="text-center"><?= Icon::bronzeScale() ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Avg.')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($kingStats as $kingId => $row) { ?>
      <tr>
        <th scope="row">
          <?= Icon::s3BossSalmonid($kings[$kingId] ?? null) . "\n" ?>
          <?= Html::encode(Yii::t('app-salmon-boss3', $kings[$kingId]?->name)) . "\n" ?>
        </th>
        <td class="text-center"><?= $fmt->asInteger($row['appearances']) ?></td>
        <td class="text-center"><?= $fmt->asInteger($row['defeated']) ?></td>
        <td class="text-center"><?= $fmt->asPercent(
          $row['appearances'] > 0
            ? $row['defeated'] / $row['appearances']
            : null,
          1,
        ) ?></td>
        <td class="text-center"><?= $fmt->asInteger($row['gold_scale']) ?></td>
        <td class="text-center"><?= $fmt->asInteger($row['silver_scale']) ?></td>
        <td class="text-center"><?= $fmt->asInteger($row['bronze_scale']) ?></td>
        <td class="text-center"><?php
          $total = array_reduce(
            [$row['gold_scale'], $row['silver_scale'], $row['bronze_scale']],
            fn (?int $carry, ?int $item): ?int => match (true) {
              $carry === null, $item === null => null,
              default => $carry + $item,
            },
            0,
          );
          if ($total !== null && $row['appearances'] > 0) {
            echo Html::encode(
              $fmt->asDecimal($total / $row['appearances'], 1),
            );
          }
        ?></td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
