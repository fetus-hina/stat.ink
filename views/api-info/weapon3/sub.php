<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\models\Language;
use app\models\Subweapon3;
use app\models\Subweapon3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var Subweapon3[] $subs
 * @var View $this
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<h2><?= Html::encode(Yii::t('app', 'Sub Weapon')) ?></h2>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
        <th></th>
        <th data-sort="string"><code>key</code></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Aliases')) ?></th>
<?php foreach ($langs as $i => $lang) { ?>
        <th data-sort="string">
          <?= Html::encode($lang['name']) . "\n" ?>
        </th>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($subs as $sub) { ?>
      <tr>
        <td><?= SubweaponIcon::widget(['model' => $sub]) ?></td>
        <?= Html::tag(
          'td',
          Html::tag('code', Html::encode($sub->key)),
          [
            'data' => [
              'sort-value' => $sub->key,
            ],
          ]
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          implode(', ', array_map(
            fn (Subweapon3Alias $alias): string => Html::tag('code', Html::encode($alias->key)),
            ArrayHelper::sort(
              $sub->subweapon3Aliases,
              fn (Subweapon3Alias $a, Subweapon3Alias $b): int => strcmp($a->key, $b->key),
            ),
          )),
        ) . "\n" ?>
<?php foreach ($langs as $j => $lang) { ?>
        <?= Html::tag(
          'td',
          Html::encode(Yii::t('app-subweapon3', $sub->name, [], $lang->lang)),
        ) . "\n" ?>
<?php } ?>
<?php } ?>
    </tbody>
  </table>
</div>
