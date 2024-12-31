<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\components\widgets\Icon;
use app\models\Language;
use app\models\SalmonBoss3;
use app\models\SalmonBoss3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var SalmonBoss3 $salmonids
 * @var View $this
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<h2><?= Html::encode(Yii::t('app-salmon3', 'Boss Salmonid')) ?></h2>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
        <th></th>
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
<?php foreach ($salmonids as $salmonid) { ?>
      <tr>
        <?= Html::tag('td', Icon::s3BossSalmonid($salmonid), ['class' => 'text-center']) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::tag('code', Html::encode($salmonid->key)),
          [
            'data' => [
              'sort-value' => $salmonid->key,
            ],
          ]
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          implode(', ', array_map(
            fn (SalmonBoss3Alias $alias): string => Html::tag('code', Html::encode($alias->key)),
            ArrayHelper::sort(
              $salmonid->salmonBoss3Aliases,
              fn (SalmonBoss3Alias $a, SalmonBoss3Alias $b): int => strcmp($a->key, $b->key),
            ),
          )),
        ) . "\n" ?>
<?php foreach ($langs as $j => $lang) { ?>
        <?= Html::tag(
          'td',
          ApiInfoName::widget([
            'name' => Yii::t('app-salmon-boss3', $salmonid->name, [], $lang->lang),
            'enName' => $salmonid->name,
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
