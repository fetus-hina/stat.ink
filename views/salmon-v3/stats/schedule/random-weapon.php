<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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

if (!$weaponStats || !$weapons) {
  return;
}

$data = ArrayHelper::map(
  $weapons,
  'id',
  function (SalmonWeapon3 $weapon) use ($weaponStats): array {
    return [
      'id' => $weapon->id,
      'key' => $weapon->key,
      'name' => Yii::t('app-weapon3', $weapon->name),
      'rank' => $weapon->rank,
      'count' => array_sum([
        ArrayHelper::getValue($weaponStats, [$weapon->id, 'normal_waves'], 0),
        ArrayHelper::getValue($weaponStats, [$weapon->id, 'xtra_waves'], 0),
      ]),
    ];
  },
);

usort(
    $data,
    fn (array $a, array $b): int => $b['count'] <=> $a['count']
        ?: $a['rank'] <=> $b['rank']
        ?: $a['id'] <=> $b['id'],
);

$totalCount = array_sum(
  array_map(
    fn (array $info): int => $info['count'],
    $data,
  ),
);

?>
<h3 id="random-weapon"><?= Html::encode(Yii::t('app-salmon3', 'Supplied Weapons')) ?></h3>
<div class="clearfix mb-3">
<?php foreach ($data as $info) { ?>
  <?= $this->render(
    'random-weapon/weapon',
    array_merge(
      compact('totalCount'),
      $info,
    ),
  ) . "\n" ?>
<?php } ?>
</div>
