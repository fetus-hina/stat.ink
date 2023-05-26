<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\models\Rule3;
use app\models\Season3;
use app\models\Special3;
use app\models\StatSpecialUse3;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Season3 $season
 * @var StatSpecialUse3[] $total
 * @var array<int, Rule3> $rules
 * @var array<int, Special3> $specials
 * @var array<int, StatSpecialUse3[]> $data
 * @var float|null $maxAvgUses
 */

TableResponsiveForceAsset::register($this);

$fmt = Yii::$app->formatter;

/**
 * @var array<int, array<int, StatSpecialUse3>> $data2
 */
$data2 = ArrayHelper::map(
  ArrayHelper::toFlatten($data),
  'rule_id',
  fn (StatSpecialUse3 $v): StatSpecialUse3 => $v,
  'special_id',
);

/**
 * @var array<int, StatSpecialUse3> $total2
 */
$total2 = ArrayHelper::index($total, 'special_id');

?>
<div class="mb-3">
  <div class="table-responsive table-responsive-force">
    <div class="grid-view">
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th><?= Html::encode(Yii::t('app', 'Special')) ?></th>
            <th width="14%"><?= Html::encode(Yii::t('app', 'Avg. Uses')) ?></th>
<?php foreach ($rules as $rule) { ?>
            <?= Html::tag(
              'th',
              Html::encode(Yii::t('app-rule3', $rule->name)),
              ['width' => '14%'],
            ) . "\n" ?>
<?php } ?>
          </tr>
        </thead>
        <tbody>
<?php foreach ($specials as $spId => $special) { ?>
          <tr>
            <?= Html::tag(
              'td',
              Html::a(
                Html::encode(Yii::t('app-special3', $special->name)),
                ['entire/special-use3-per-special',
                  'season' => $season->id,
                  'special' => $special->key,
                ],
              ),
            ) . "\n" ?>
            <?= Html::tag(
              'td',
              $this->render('avg-uses', [
                'model' => $total2[$spId] ?? null,
                'maxAvgUses' => $maxAvgUses,
              ]),
            ) . "\n" ?>
<?php foreach ($rules as $rId => $rule) { ?>
            <?= Html::tag(
              'td',
              $this->render('avg-uses', [
                'model' => $data2[$spId][$rId] ?? null,
                'maxAvgUses' => $maxAvgUses,
              ]),
            ) . "\n" ?>
<?php } ?>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
