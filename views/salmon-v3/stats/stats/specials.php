<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Salmon3UserStatsSpecial;
use app\models\Special3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<int, Salmon3UserStatsSpecial> $specialStats
 * @var array<int, Special3> $specials
 */

if (!$specialStats) {
  return;
}

$fmt = Yii::$app->formatter;

?>
<h3><?= Html::encode(Yii::t('app', 'Specials')) ?></h3>
<div class="table-responsive">
  <table class="table table-bordered table-condensed table-striped">
    <thead>
      <tr>
        <th class="text-center"><?= Html::encode(Yii::t('app', 'Special')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Jobs')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Avg. Waves')) ?></th>
        <th class="text-center"><?= Icon::goldenEgg() ?></th>
        <th class="text-center"><?= Icon::powerEgg() ?></th>
        <th class="text-center"><?= Icon::s3Rescues() ?></th>
        <th class="text-center"><?= Icon::s3Rescued() ?></th>
        <?= Html::tag('th', Html::encode(Yii::t('app-salmon3', 'Boss')), [
          'class' => 'auto-tooltip text-center',
          'title' => Yii::t('app-salmon3', 'Boss Salmonids'),
        ]) . "\n" ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($specials as $specialId => $special) { ?>
<?php $model = $specialStats[$specialId] ?? null; ?>
<?php if ($model) { ?>
      <tr>
        <th scope="row">
          <?= Icon::s3Special($special) . "\n" ?>
          <?= Html::encode(Yii::t('app-special3', $special->name)) . "\n" ?>
        </th>
        <td class="text-center"><?= $fmt->asInteger($model->jobs) ?></th>
        <td class="text-center"><?= $fmt->asPercent($model->jobs_cleared / $model->jobs, 1) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($model->waves_cleared_avg, 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($model->golden_egg_avg, 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($model->power_egg_avg, 0) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($model->rescue_avg, 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($model->rescued_avg, 2) ?></td>
        <td class="text-center"><?= $fmt->asDecimal($model->defeat_boss_avg, 2) ?></td>
      </tr>
<?php } ?>
<?php } ?>
    </tbody>
  </table>
</div>
