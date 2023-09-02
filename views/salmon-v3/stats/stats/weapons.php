<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Salmon3UserStatsWeapon;
use app\models\SalmonWeapon3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, Salmon3UserStatsWeapon> $weaponStats
 * @var array<int, SalmonWeapon3> $weapons
 */

if (!$weaponStats) {
  return;
}

$normalWaves = array_sum(ArrayHelper::getColumn($weaponStats, 'normal_waves'));
$xtraWaves = array_sum(ArrayHelper::getColumn($weaponStats, 'xtra_waves'));
if ($normalWaves < 1 || $xtraWaves < 0) {
  return;
}

$fmt = Yii::$app->formatter;

?>
<h3><?= Html::encode(Yii::t('app-salmon3', 'Supplied Weapons')) ?></h3>
<div class="table-responsive">
  <?= Html::beginTag('table', [
    'class' => [
      'table',
      'table-bordered',
      'table-condensed',
      'table-striped',
      'mb-0',
    ],
  ]) . "\n" ?>
    <thead>
      <tr>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Total')) ?></th>
        <th class="text-center" colspan="2"><?= Html::encode(Yii::t('app-salmon3', 'Normal Waves')) ?></th>
        <th class="text-center" colspan="2"><?= Html::encode(Yii::t('app-salmon3', 'XTRAWAVE')) ?></th>
      </tr>
      <tr>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Defeat %')) ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?= Html::tag(
          'th',
          Html::encode(sprintf('(%s)', Yii::t('app', 'Total'))),
          [
            'class' => 'text-center',
            'scope' => 'row',
          ],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($normalWaves + $xtraWaves)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($normalWaves)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode(
            $fmt->asPercent(
              array_sum(ArrayHelper::getColumn($weaponStats, 'normal_waves_cleared')) / $normalWaves,
              1,
            ),
          ),
          ['class' => 'text-center'],
        ) ?>
<?php if ($xtraWaves > 0) { ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($xtraWaves)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode(
            $fmt->asPercent(
              array_sum(ArrayHelper::getColumn($weaponStats, 'xtra_waves_cleared')) / $xtraWaves,
              1,
            ),
          ),
          ['class' => 'text-center'],
        ) ?>
<?php } else { ?>
        <td></td>
        <td></td>
        <td></td>
<?php } ?>
      </tr>
<?php foreach ($weaponStats as $weaponId => $model) { ?>
      <tr>
        <?= Html::tag(
          'th',
          implode(' ', [
            Icon::s3Weapon($weapons[$weaponId] ?? null),
            Html::encode(Yii::t('app-weapon3', $weapons[$weaponId]?->name ?? '?')),
          ]),
          ['scope' => 'row'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($model->total_waves)),
          ['class' => 'text-center'],
        ) . "\n" ?>
<?php if ($model->normal_waves > 0) { ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($model->normal_waves)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asPercent($model->normal_waves_cleared / $model->normal_waves, 1)),
          ['class' => 'text-center'],
        ) . "\n" ?>
<?php } else { ?>
        <td></td>
        <td></td>
<?php } ?>
<?php if ($model->xtra_waves > 0) { ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($model->xtra_waves)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asPercent($model->xtra_waves_cleared / $model->xtra_waves, 1)),
          ['class' => 'text-center'],
        ) . "\n" ?>
<?php } else { ?>
        <td></td>
        <td></td>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
