<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Splatfest3;
use app\models\Splatfest3StatsPower;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Splatfest3 $splatfest
 * @var View $this
 */

$abstract = $splatfest->splatfest3StatsPower;
if (
  !$abstract instanceof Splatfest3StatsPower ||
  $abstract->agg_battles < 100 ||
  $abstract->average === null ||
  $abstract->histogram_width === null ||
  $abstract->maximum === null ||
  $abstract->minimum === null ||
  $abstract->p05 === null ||
  $abstract->p25 === null ||
  $abstract->p50 === null ||
  $abstract->p75 === null ||
  $abstract->p80 === null ||
  $abstract->p95 === null ||
  $abstract->stddev === null
) {
  return;
}

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <?= Html::encode(
      vsprintf('%s - %s', [
        Yii::t('app', 'Splatfest Power'),
        Yii::t('app-lobby3', 'Splatfest (Pro)'),
      ]),
    ) . "\n" ?>
  </div>
  <div class="panel-body pb-0">
    <?= $this->render('power/alert-aggregate-target') . "\n" ?>
    <?= $this->render('power/table', compact('abstract')) . "\n" ?>
    <?= $this->render('power/histogram', compact('splatfest')) . "\n" ?>
  </div>
</div>
