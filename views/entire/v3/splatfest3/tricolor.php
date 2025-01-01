<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
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

$fmt = Yii::$app->formatter;

$totalWins = (int)array_sum(ArrayHelper::getColumn($tricolorStats, 'attacker_wins'));
$totalBattles = (int)array_sum(ArrayHelper::getColumn($tricolorStats, 'battles'));

$errInfo = StandardError::winpct($totalWins, $totalBattles);

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <?= implode(' ', [
      Html::encode(Yii::t('app-rule3', 'Tricolor Battle')),
      '-',
      Icon::s3TricolorAttacker(),
      Html::encode(Yii::t('app', 'Attacker Team Win Rate')),
    ]) . "\n" ?>
  </div>
  <div class="panel-body pb-0">
<?php if ($totalWins < 10 || $totalBattles < 100 || !$errInfo) { ?>
    <p class="text-muted mb-3">
      <?= Html::encode(
        Yii::t('app', 'Not enough data is available.'),
      ) . "\n" ?>
    </p>
<?php } else { ?>
    <div class="mb-3">
      <?= $this->render('tricolor/table-attacker', [
        'stages' => $stages,
        'tricolorStats' => $tricolorStats,
      ]) . "\n" ?>
    </div>
    <div class="mb-3">
      <?= $this->render('tricolor/chart-attacker', [
        'stages' => $stages,
        'tricolorStats' => $tricolorStats,
      ]) . "\n" ?>
    </div>
<?php } ?>
  </div>
</div>
