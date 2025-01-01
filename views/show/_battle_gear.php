<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Battle;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle $battle
 * @var View $this
 */

$headgear = $battle->headgear;
$clothing = $battle->clothing;
$shoes = $battle->shoes;

$gears = [$headgear, $clothing, $shoes];
?>
<table class="table table-bordered table-condensed" style="margin-bottom:0">
  <thead>
    <tr>
      <th></th>
      <th><?= Html::encode(Yii::t('app-gear', 'Headgear')) ?></th>
      <th><?= Html::encode(Yii::t('app-gear', 'Clothing')) ?></th>
      <th><?= Html::encode(Yii::t('app-gear', 'Shoes')) ?></th>
    </tr>
  </thead>
  <tbody>
<?php if ($headgear->gear_id || $clothing->gear_id || $shoes->gear_id): ?>
    <tr>
      <th><?= Html::encode(Yii::t('app', 'Gear')) ?></th>
<?php foreach ($gears as $gear): ?>
      <td><?= Html::encode($gear->gear_id ? Yii::t('app-gear', $gear->gear->name) : '?') ?></td>
<?php endforeach ?>
    </tr>
<?php endif ?>
    <tr>
      <th><?= Html::encode(Yii::t('app', 'Primary Ability')) ?></th>
<?php foreach ($gears as $gear): ?>
      <td><?= Html::encode($gear->primaryAbility ? Yii::t('app-ability', $gear->primaryAbility->name) : '') ?></td>
<?php endforeach ?>
    </tr>
<?php foreach (range(0, 2) as $i): ?>
    <tr>
<?php if ($i === 0): ?>
      <th rowspan="3"><?= Html::encode(Yii::t('app', 'Secondary Abilities')) ?></th>
<?php endif ?>
<?php foreach ($gears as $gear): ?>
      <td><?=
        Html::encode((count($gear->secondaries) > $i)
          ? ($gear->secondaries[$i]->ability ?? null)
            ? Yii::t('app-ability', $gear->secondaries[$i]->ability->name)
            : Yii::t('app-ability', '(Locked)')
          : ''
        )
      ?></td>
<?php endforeach ?>
    </tr>
<?php endforeach ?>
  </tbody>
</table>
<p class="text-right">
  <a href="#effect"><?= Html::encode(Yii::t('app', 'Ability Effect')) ?></a>
</p>
