<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Map3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, Map3> $stages
 * @var array{map_id: int, battles: int, attacker_wins: int}[] $tricolorStats
 */

?>
<div class="table-responsive mb-0">
  <table class="table table-bordered w-auto mb-0">
    <thead>
      <tr>
        <th></th>
        <th><?= Html::encode(Yii::t('app', 'Samples')) ?></th>
        <th>
          <?= Icon::s3TricolorAttacker() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'Wins')) . "\n" ?>
        </th>
        <th>
          <?= Icon::s3TricolorAttacker() . "\n" ?>
          <?= Html::encode(Yii::t('app', 'Win %')) . "\n" ?>
          <?= Html::encode(sprintf('(%s)', Yii::t('app', '{pct}% CI', ['pct' => 95]))) . "\n" ?>
        </th>
      </tr>
    </thead>
    <tbody>
<?php if (count($tricolorStats) > 1) { ?>
      <?= $this->render('table-attacker/row', [
        'label' => Yii::t('app', 'Total'),
        'shortLabel' => null,
        'battles' => (int)array_sum(ArrayHelper::getColumn($tricolorStats, 'battles')),
        'wins' => (int)array_sum(ArrayHelper::getColumn($tricolorStats, 'attacker_wins')),
      ]) . "\n" ?>
<?php } ?>
<?php foreach ($tricolorStats as $row) { ?>
      <?= $this->render('table-attacker/row', [
        'label' => Yii::t('app-map3', $stages[$row['map_id']]?->name)
          ?: sprintf('(#%d)', $row['map_id']),
        'shortLabel' => Yii::t('app-map3', $stages[$row['map_id']]?->short_name)
          ?: Yii::t('app-map3', $stages[$row['map_id']]?->name)
          ?: sprintf('(#%d)', $row['map_id']),
        'battles' => (int)$row['battles'],
        'wins' => (int)$row['attacker_wins'],
      ]) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
