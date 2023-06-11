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
 *    gold_scale: int,
 *    silver_scale: int,
 *    bronze_scale: int
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
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'King Salmonid')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Appearances')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeated')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeat %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Fish Scales')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($kingStats as $kingId => $row) { ?>
      <tr>
        <th scope="row"><?= Html::encode(Yii::t('app-salmon-boss3', $kings[$kingId]?->name)) ?></th>
        <td class="text-right"><?= $fmt->asInteger($row['appearances']) ?></td>
        <td class="text-right"><?= $fmt->asInteger($row['defeated']) ?></td>
        <td class="text-right"><?= $fmt->asPercent(
          $row['appearances'] > 0
            ? $row['defeated'] / $row['appearances']
            : null,
          1,
        ) ?></td>
        <td class="text-center"><?= implode('', [
          $renderScale(Icon::goldScale(), $row['gold_scale']),
          $renderScale(Icon::silverScale(), $row['silver_scale']),
          $renderScale(Icon::bronzeScale(), $row['bronze_scale']),
        ]) ?></td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
