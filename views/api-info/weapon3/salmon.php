<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\components\widgets\FA;
use app\components\widgets\Icon;
use app\models\Language;
use app\models\SalmonWeapon3;
use app\models\SalmonWeapon3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var SalmonWeapon3[] $weapons
 * @var View $this
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<?= Html::tag(
  'h2',
  implode(' ', [
    Html::encode(Yii::t('app', 'Rare Weapon')),
  ]),
) . "\n" ?>
<?= Html::tag(
  'p',
  implode(' ', [
    Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format')),
      ]),
      ['api-v3/salmon-weapon'],
      ['class' => 'label label-default'],
    ),
    Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format (All langs)')),
      ]),
      ['api-v3/salmon-weapon', 'full' => 1],
      ['class' => 'label label-default'],
    ),
  ]),
) . "\n" ?>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
        <th></th>
        <th><?= Icon::s3Salmon() ?></th>
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
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($weapons as $weapon) { ?>
      <tr>
        <td><?= Icon::s3Weapon($weapon) ?></td>
        <td><?= Icon::s3Salmon() ?></td>
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
            fn (SalmonWeapon3Alias $alias): string => Html::tag('code', Html::encode($alias->key)),
            ArrayHelper::sort(
              $weapon->salmonWeapon3Aliases,
              fn (SalmonWeapon3Alias $a, SalmonWeapon3Alias $b): int => strcmp($a->key, $b->key),
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
<?php } ?>
<?php } ?>
    </tbody>
  </table>
</div>
