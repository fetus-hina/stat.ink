<?php

use app\components\helpers\Html;
use app\models\Battle;

/**
 * @var Battle $battle
 */

if (!$attack = $battle->weaponAttack) {
  return '';
}

$effects = $battle->abilityEffects;
$attackPct = $effects->attackPct;
$baseHit2Kill = $attack->getHitToKill();
$damageCap = $attack->getDamageCap();

$f = Yii::$app->formatter;
?>
<?= Html::encode($f->asPercent($attackPct, 1)) . "\n" ?>
<?= vsprintf('[%s × %s = %s]', [
  Html::encode($f->asDecimal($attack->damage, 1)),
  Html::encode($f->asPercent($attackPct, 1)),
  Html::tag('strong', Html::encode($f->asDecimal((float)$attack->damage * $attackPct, 1))),
]) . "\n" ?>
<table class="table table-bordered table-condensed hidden-xs">
  <thead>
    <tr>
      <th colspan="2" rowspan="2"><?= Html::encode(Yii::t('app-ability', 'Defense Up')) ?></th>
      <th colspan="10"><?= Html::encode(Yii::t('app', 'Secondary Abilities')) ?></th>
    </tr>
    <tr>
      <th class="text-center">0</th>
      <th class="text-center">1</th>
      <th class="text-center">2</th>
      <th class="text-center">3</th>
      <th class="text-center">4</th>
      <th class="text-center">5</th>
      <th class="text-center">6</th>
      <th class="text-center">7</th>
      <th class="text-center">8</th>
      <th class="text-center">9</th>
    </tr>
  </thead>
  <tbody>
<?php foreach (range(0, 3) as $defMain): ?>
    <tr>
<?php if ($defMain === 0): ?>
      <th scope="row" rowspan="4"><?= Html::encode(Yii::t('app', 'Primary Ability')) ?></th>
<?php endif ?>
      <th scope="row" class="text-center"><?= Html::encode($f->asInteger($defMain)) ?></th>
<?php foreach (range(0, 9) as $defSub): ?>
<?php $damage = $effects->calcDamage($attack->damage, $defMain, $defSub) ?>
<?php $hit2kill = ceil(100 / $damage) ?>
<?php if ($damage > $damageCap): ?>
        <?= Html::tag(
          'td',
          Html::encode($f->asDecimal($damageCap, 1)),
          ['class' => 'text-right auto-tooltip success', 'title' => $f->asDecimal($damage), 'style' => 'font-style:italic']
        ) . "\n" ?>
<?php else: ?>
        <?= Html::tag(
          'td',
          Html::encode($f->asDecimal($damage, 1)),
          ['class' => ($hit2kill > $baseHit2Kill) ? 'text-right danger' : 'text-right']
        ) . "\n" ?>
<?php endif ?>
<?php endforeach ?>
    </tr>
<?php endforeach ?>
  </tbody>
</table>
<table class="table table-bordered table-condensed visible-xs-block">
  <thead>
    <tr>
      <th colspan="2" rowspan="2"><?= Html::encode(Yii::t('app-ability', 'Defense Up')) ?></th>
      <th colspan="4"><?= Html::encode(Yii::t('app', 'Primary Ability')) ?></th>
    </tr>
    <tr>
      <th class="text-center">0</th>
      <th class="text-center">1</th>
      <th class="text-center">2</th>
      <th class="text-center">3</th>
    </tr>
  </thead>
  <tbody>
<?php foreach (range(0, 9) as $defSub): ?>
    <tr>
<?php if ($defSub === 0): ?>
      <th scope="row" rowspan="10"><?= Html::encode(Yii::t('app', 'Secondary Abilities')) ?></th>
<?php endif ?>
      <th scope="row" class="text-center"><?= Html::encode($f->asInteger($defSub)) ?></th>
<?php foreach (range(0, 3) as $defMain): ?>
<?php $damage = $effects->calcDamage($attack->damage, $defMain, $defSub) ?>
<?php $hit2kill = ceil(100 / $damage) ?>
<?php if ($damage > $damageCap): ?>
        <?= Html::tag(
          'td',
          Html::encode($f->asDecimal($damageCap, 1)),
          ['class' => 'text-right auto-tooltip success', 'title' => $f->asDecimal($damage), 'style' => 'font-style:italic']
        ) . "\n" ?>
<?php else: ?>
        <?= Html::tag(
          'td',
          Html::encode($f->asDecimal($damage, 1)),
          ['class' => ($hit2kill > $baseHit2Kill) ? 'text-right danger' : 'text-right']
        ) . "\n" ?>
<?php endif ?>
<?php endforeach ?>
    </tr>
<?php endforeach ?>
  </tbody>
</table>
