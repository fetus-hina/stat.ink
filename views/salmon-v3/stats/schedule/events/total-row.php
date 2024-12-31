<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\EventTrait;
use app\components\helpers\TypeHelper;
use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type EventStats from EventTrait
 *
 * @var EventStats $eventStats
 * @var User $user
 * @var View $this
 * @var array<int, SalmonWaterLevel2> $tides
 */

$totalWaves = 0;
$totalWavesPerTide = [];
$clearedPerTide = [];
$deliveredPerTide = [];
$quotaPerTide = [];
foreach ($eventStats as $row1) {
  foreach ($row1 as $id2 => $row2) {
    $totalWaves += $row2['waves'];
    $totalWavesPerTide[$id2] = ($totalWavesPerTide[$id2] ?? 0) + $row2['waves'];
    $clearedPerTide[$id2] = ($clearedPerTide[$id2] ?? 0) + $row2['cleared'];
    $deliveredPerTide[$id2] = ($deliveredPerTide[$id2] ?? 0) + $row2['total_delivered'];
    $quotaPerTide[$id2] = ($quotaPerTide[$id2] ?? 0) + $row2['total_quota'];
  }
}

if ($totalWaves < 1) {
  return;
}

?>
<tr>
  <?= Html::tag(
    'th',
    Html::encode(sprintf('(%s)', Yii::t('app', 'Total'))),
    ['class' => 'text-center'],
  ) . "\n" ?>
<?php foreach ($tides as $tide) { ?>
  <?= $this->render('event-row/tide', [
    'cleared' => $clearedPerTide[$tide->id] ?? null,
    'tideWaves' => $totalWavesPerTide[$tide->id] ?? null,
    'totalDelivered' => $deliveredPerTide[$tide->id] ?? null,
    'totalQuota' => $quotaPerTide[$tide->id] ?? null,
    'totalWaves' => $totalWaves,
    'waves' => $totalWavesPerTide[$tide->id] ?? null,
  ]) . "\n" ?>
<?php } ?>
</tr>
