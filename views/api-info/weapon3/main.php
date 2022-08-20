<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\models\Language;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var View $this
 * @var Weapon3[] $weapons
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<h2><?= Html::encode(Yii::t('app', 'Main Weapon')) ?></h2>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
        <th data-sort="int"><?= Html::encode(Yii::t('app', 'Category')) ?></th>
        <th data-sort="string"><code>key</code></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Aliases')) ?></th>
<?php foreach ($langs as $i => $lang) { ?>
        <th data-sort="string">
          <?= Html::encode($lang['name']) . "\n" ?>
        </th>
<?php if ($i === 0) { ?>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Main Weapon')) ?></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Sub Weapon')) ?></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Special')) ?></th>
<?php } ?>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($weapons as $weapon) { ?>
      <tr>
        <?= Html::tag(
          'td',
          Html::encode(Yii::t('app-weapon3', $weapon->mainweapon->type->name)),
          [
            'data' => [
              'sort-value' => $weapon->mainweapon->type->rank,
            ],
          ],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::tag('code', Html::encode($weapon->key)),
          [
            'data' => [
              'sort-value' => $weapon->key,
            ],
          ]
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          implode(', ', array_map(
            fn (Weapon3Alias $alias): string => Html::tag('code', Html::encode($alias->key)),
            ArrayHelper::sort(
              $weapon->weapon3Aliases,
              fn (Weapon3Alias $a, Weapon3Alias $b): int => strcmp($a->key, $b->key),
            ),
          )),
        ) . "\n" ?>
<?php foreach ($langs as $j => $lang) { ?>
        <?= Html::tag(
          'td',
          Html::encode(Yii::t('app-weapon3', $weapon->name, [], $lang->lang)),
        ) . "\n" ?>
<?php if ($j === 0) { ?>
        <?= Html::tag(
          'td',
          Html::encode(Yii::t('app-weapon3', $weapon->mainweapon->name, [], $lang->lang)),
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $weapon->subweapon
            ? Html::encode(Yii::t('app-subweapon3', $weapon->subweapon->name, [], $lang->lang))
            : '',
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $weapon->special
            ? Html::encode(Yii::t('app-special3', $weapon->special->name, [], $lang->lang))
            : '',
        ) . "\n" ?>
<?php } ?>
<?php } ?>
<?php } ?>
    </tbody>
  </table>
</div>
