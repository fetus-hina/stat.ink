<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\Rule3;
use app\models\Special3;
use app\models\StatSpecialUse3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatSpecialUse3[] $total
 * @var array<int, Rule3> $rules
 * @var array<int, Special3> $specials
 * @var array<int, StatSpecialUse3[]> $data
 */

TableResponsiveForceAsset::register($this);

$fmt = Yii::$app->formatter;
$modeIconAsset = GameModeIconsAsset::register($this);

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
            <th><?= Html::encode(Yii::t('app', 'Avg. Uses')) ?></th>
<?php foreach ($rules as $rule) { ?>
            <?= Html::tag('th', vsprintf('%s %s', [
              Html::img(
                Yii::$app->assetManager->getAssetUrl($modeIconAsset, sprintf('spl3/%s.png', $rule->key)),
                [
                  'class' => 'basic-icon',
                  'draggable' => 'false',
                ],
              ),
              Html::encode(Yii::t('app-rule3', $rule->name)),
            ])) . "\n" ?>
<?php } ?>
          </tr>
        </thead>
        <tbody>
<?php foreach ($specials as $spId => $special) { ?>
          <tr>
            <?= Html::tag('td', vsprintf('%s %s', [
              SpecialIcon::widget(['model' => $special]),
              Html::encode(Yii::t('app-special3', $special->name)),
            ])) . "\n" ?>
            <?= Html::tag(
              'td',
              ($v = $total2[$spId] ?? null)
                ? (
                  $v->stddev !== null
                    ? vsprintf('%s (σ=%s)', [
                      $fmt->asDecimal($v->avg_uses, 2),
                      $fmt->asDecimal($v->stddev, 2),
                    ])
                    : $fmt->asDecimal($v->avg_uses, 2)
                )
                : '',
              ['class' => 'text-right'],
            ) . "\n" ?>
<?php foreach ($rules as $rId => $rule) { ?>
            <?= Html::tag(
              'td',
              ($v = $data2[$spId][$rId] ?? null)
                ? (
                  $v->stddev !== null
                    ? vsprintf('%s (σ=%s)', [
                      $fmt->asDecimal($v->avg_uses, 2),
                      $fmt->asDecimal($v->stddev, 2),
                    ])
                    : $fmt->asDecimal($v->avg_uses, 2)
                )
                : '',
              ['class' => 'text-right'],
            ) . "\n" ?>
<?php } ?>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
