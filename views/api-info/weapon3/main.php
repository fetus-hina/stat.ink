<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\components\widgets\FA;
use app\components\widgets\Icon;
use app\models\Language;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use app\models\XMatchingGroup3;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var View $this
 * @var Weapon3[] $weapons
 * @var array<string, XMatchingGroup3> $matchingGroups
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

$salmonIcon = 'SR';

?>
<h2><?= Html::encode(Yii::t('app', 'Main Weapon')) ?></h2>
<?= Html::tag(
  'p',
  implode(' ', [
    Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format')),
      ]),
      ['api-v3/weapon'],
      ['class' => 'label label-default'],
    ),
    Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format (All langs)')),
      ]),
      ['api-v3/weapon', 'full' => 1],
      ['class' => 'label label-default'],
    ),
  ]),
) . "\n" ?>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
        <th data-sort="int">X</th>
        <th data-sort="int"><?= Html::encode(Yii::t('app', 'Category')) ?></th>
        <?= Html::tag('th', Html::encode($salmonIcon), [
          'class' => 'auto-tooltip',
          'data-sort' => 'int',
          'title' => Yii::t('app-salmon2', 'Salmon Run'),
        ]) . "\n" ?>
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
        <?= $this->render('main/td-x-matching', [
          'weapon' => $weapon,
          'group' => $matchingGroups[$weapon->key] ?? null,
        ]) . "\n" ?>
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
<p class="text-right mt-2">
  [<?= Html::encode(Yii::t('app-xmatch3', 'X: Match making group')) ?>]
  <?= Yii::t(
    'app',
    'Source: {source}',
    [
      'source' => Html::a(
        'Twitter @antariska_spl',
        str_starts_with(Yii::$app->language, 'ja')
          ? 'https://twitter.com/antariska_spl/status/1610201648378556418'
          : 'https://twitter.com/antariska_spl/status/1610203442114629632',
        [
          'target' => '_blank',
          'rel' => 'noopener noreferrer',
        ],
      ),
    ],
  ) . "\n" ?>
</p>
