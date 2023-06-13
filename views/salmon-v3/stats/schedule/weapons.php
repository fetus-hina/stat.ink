<?php

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\WeaponTrait;
use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use app\models\SalmonWeapon3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type WeaponStats from WeaponTrait
 *
 * @var SalmonSchedule3 $schedule
 * @var View $this
 * @var array<int, SalmonWeapon3> $weapons
 * @var array<int, WeaponStats> $weaponStats
 */

if (!$weaponStats) {
  return;
}

$normalWaves = array_sum(ArrayHelper::getColumn($weaponStats, 'normal_waves'));
$xtraWaves = array_sum(ArrayHelper::getColumn($weaponStats, 'xtra_waves'));
if ($normalWaves < 1 || $xtraWaves < 0) {
  return;
}

$isRandomSchedule = array_sum(
  array_map(
    fn (SalmonScheduleWeapon3 $w): int => $w->random_id === null ? 0 : 1,
    $schedule->salmonScheduleWeapon3s,
  ),
) > 0;

$fmt = Yii::$app->formatter;

?>
<h3><?= Html::encode(Yii::t('app-salmon3', 'Loaned Weapons')) ?></h3>
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
        <th class="text-center" colspan="2"><?= Html::encode(Yii::t('app', 'Total')) ?></th>
        <th class="text-center" colspan="3"><?= Html::encode(Yii::t('app-salmon3', 'Normal Waves')) ?></th>
        <th class="text-center" colspan="3"><?= Html::encode(Yii::t('app-salmon3', 'Xtrawave')) ?></th>
      </tr>
      <tr>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Loan %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Loan %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Loan %')) ?></th>
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
        <td class="text-center">-</td>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($normalWaves)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <td class="text-center">-</td>
        <?= Html::tag(
          'td',
          Html::encode(
            $fmt->asPercent(
              array_sum(ArrayHelper::getColumn($weaponStats, 'normal_cleared')) / $normalWaves,
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
        <td class="text-center">-</td>
        <?= Html::tag(
          'td',
          Html::encode(
            $fmt->asPercent(
              array_sum(ArrayHelper::getColumn($weaponStats, 'xtra_cleared')) / $xtraWaves,
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
<?php foreach ($weaponStats as $weaponId => $row) { ?>
      <tr>
        <?= Html::tag(
          'th',
          Html::encode(Yii::t('app-weapon3', $weapons[$weaponId]?->name ?? '?')),
          ['scope' => 'row'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($row['normal_waves'] + $row['xtra_waves'])),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode(
            $fmt->asPercent(
              ($row['normal_waves'] + $row['xtra_waves']) / ($normalWaves + $xtraWaves),
              2,
            ),
          ),
          ['class' => 'text-center'],
        ) . "\n" ?>
<?php if ($row['normal_waves'] > 0) { ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($row['normal_waves'])),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asPercent($row['normal_waves'] / $normalWaves, 2)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asPercent($row['normal_cleared'] / $row['normal_waves'], 1)),
          ['class' => 'text-center'],
        ) . "\n" ?>
<?php } else { ?>
        <td></td>
        <td></td>
        <td></td>
<?php } ?>
<?php if ($row['xtra_waves'] > 0) { ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($row['xtra_waves'])),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asPercent($row['xtra_waves'] / $xtraWaves, 2)),
          ['class' => 'text-center'],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asPercent($row['xtra_cleared'] / $row['xtra_waves'], 1)),
          ['class' => 'text-center'],
        ) . "\n" ?>
<?php } else { ?>
        <td></td>
        <td></td>
        <td></td>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
<?= Html::tag(
  'p',
  implode(' ', array_filter([
    Yii::t(
      'app-salmon3',
      'Note that this data is too small data size to speak of weapon loan rates.',
    ),
    $isRandomSchedule
      ? Yii::t('app-salmon3', 'For a more accurate weapon loan rate, see {link}.', [
        'link' => Html::a(
          Yii::t('app-salmon3', 'Random Loan Rate'),
          ['entire/salmon3-random-loan',
            'id' => $schedule->id,
          ],
        ),
      ])
      : null,
  ])),
  [
    'class' => [
      'mb-3',
      'mt-1',
      'small',
      'text-muted',
    ],
  ],
) . "\n" ?>
