<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Map3;
use app\models\SalmonBoss3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<int, SalmonBoss3> $bosses
 * @var array<int, array{boss_id: int, appearances: int, defeated: int, defeated_by_me: int}> $bossStats
 */

if (!$bossStats) {
  return;
}

$fmt = Yii::$app->formatter;

?>
<h3><?= Html::encode(Yii::t('app-salmon2', 'Boss Salmonids')) ?></h3>
<div class="table-responsive">
  <table class="table table-bordered table-condensed table-striped">
    <thead>
      <tr>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon2', 'Boss Salmonid')) ?></th>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon3', 'Appearances')) ?></th>
        <th class="text-center" colspan="2"><?= Html::encode(Yii::t('app-salmon3', 'Defeated')) ?></th>
        <th class="text-center" colspan="3">
          <?= Icon::user() . "\n" ?>
          <?= Html::encode($user->name) . "\n" ?>
        </th>
      </tr>
      <tr>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeated')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeat %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeated')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Contribution')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeat %')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($bossStats as $bossId => $row) { ?>
      <tr>
        <th scope="row">
          <?= Icon::s3BossSalmonid($bosses[$bossId] ?? null) . "\n" ?>
          <?= Html::encode(Yii::t('app-salmon-boss3', $bosses[$bossId]?->name)) . "\n" ?>
        </th>
        <td class="text-center"><?= $fmt->asInteger($row['appearances']) ?></td>
        <td class="text-center"><?= $fmt->asInteger($row['defeated']) ?></td>
        <td class="text-center"><?= $fmt->asPercent(
          $row['appearances'] > 0
            ? $row['defeated'] / $row['appearances']
            : null,
          1,
        ) ?></td>
        <td class="text-center"><?= $fmt->asInteger($row['defeated_by_me']) ?></td>
        <td class="text-center"><?php
          $v = $row['defeated'] > 0 ? $row['defeated_by_me'] / $row['defeated'] : null;
          if ($v !== null) {
            echo Html::tag(
              'span',
              $fmt->asPercent($v, 1),
              [
                'class' => [
                  'label',
                  $v >= 0.25 ? 'label-success' : 'label-danger',
                ]
              ],
            );
          }
        ?></td>
        <td class="text-center"><?= $fmt->asPercent(
          $row['appearances'] > 0
            ? $row['defeated_by_me'] / $row['appearances']
            : null,
          1,
        ) ?></td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
