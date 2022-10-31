<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Language;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var View $this
 * @var Weapon3[] $weapons
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

$salmonIcon = Html::img(
  Yii::$app->assetManager->getAssetUrl(GameModeIconsAsset::register($this), 'spl3/salmon.png'),
  [
    'alt' => '🐟',
    'style' => [
      'height' => '1em',
      'width' => 'auto',
    ],
  ],
);

?>
<h2><?= Html::encode(Yii::t('app', 'Main Weapon')) ?></h2>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
        <th></th>
        <th data-sort="int"><?= Html::encode(Yii::t('app', 'Category')) ?></th>
        <th data-sort="int"><?= $salmonIcon ?></th>
        <th data-sort="string"><code>key</code></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Aliases')) ?></th>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag('th', Html::encode($lang->name), [
          'class' => $lang->htmlClasses,
          'data' => [
            'sort' => 'string',
          ],
          'lang' => $lang->lang,
        ]) . "\n" ?>
<?php if ($i === 0) { ?>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Weapon (Short)')) ?></th>
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
        <td><?= WeaponIcon::widget(['model' => $weapon]) ?></td>
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
          $weapon->salmonWeapon3 ? $salmonIcon : '',
          [
            'data' => [
              'sort-value' => $weapon->salmonWeapon3 ? 1 : 0,
            ],
          ]
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
          ApiInfoName::widget([
            'name' => Yii::t('app-weapon3', $weapon->name, [], $lang->lang),
            'enName' => $weapon->name,
            'lang' => $lang->lang,
          ]),
          [
            'class' => $lang->htmlClasses,
            'lang' => $lang->lang,
          ]
        ) . "\n" ?>
<?php if ($j === 0) { ?>
        <?= $this->render('main/short-name', [
          'name' => Yii::t('app-weapon3', $weapon->name, [], $lang->lang),
        ]) . "\n" ?>
        <?= Html::tag(
          'td',
          implode(' ', [
            WeaponIcon::widget([
              'model' => $weapon->mainweapon,
              'alt' => false,
            ]),
            $weapon->name === $weapon->mainweapon->name
              ? Html::tag(
                'span',
                Html::encode(Yii::t('app-weapon3', $weapon->mainweapon->name, [], $lang->lang)),
                ['class' => 'text-muted']
              )
              : Html::encode(Yii::t('app-weapon3', $weapon->mainweapon->name, [], $lang->lang)),
          ])
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $weapon->subweapon
            ? implode(' ', [
              SubweaponIcon::widget(['model' => $weapon->subweapon, 'alt' => false]),
              Html::encode(Yii::t('app-subweapon3', $weapon->subweapon->name, [], $lang->lang)),
            ])
            : '',
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $weapon->special
            ? implode(' ', [
              SpecialIcon::widget(['model' => $weapon->special, 'alt' => false]),
              Html::encode(Yii::t('app-special3', $weapon->special->name, [], $lang->lang)),
            ])
            : '',
        ) . "\n" ?>
<?php } ?>
<?php } ?>
<?php } ?>
    </tbody>
  </table>
</div>
