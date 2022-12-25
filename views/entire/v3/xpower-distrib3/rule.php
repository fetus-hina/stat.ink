<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatXPowerDistrib3;
use app\models\StatXPowerDistribAbstract3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var Season3 $season
 * @var StatXPowerDistribAbstract3|null $abstract
 * @var View $this
 */

?>
<div class="mb-3">
  <?= $this->render('rule/heading', compact('rule')) . "\n" ?>
  <?= $this->render('rule/histogram', [
    'data' => StatXPowerDistrib3::find()
      ->andWhere([
        'season_id' => $season->id,
        'rule_id' => $rule->id,
      ])
      ->orderBy(['x_power' => SORT_ASC])
      ->all(),
  ]) . "\n" ?>
  <?= $this->render('rule/abstract', ['model' => $abstract]) . "\n" ?>
</div>
