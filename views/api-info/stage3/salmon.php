<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\components\widgets\Icon;
use app\models\SalmonMap3;
use app\models\SalmonMap3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var SalmonMap3[] $stages
 * @var View $this
 * @var array[] $langs
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

$fmt = Yii::$app->formatter;

?>
<h2 id="salmon3">
  <?= Html::encode(Yii::t('app-salmon3', 'Salmon Run')) . "\n" ?>
</h2>
<div class="table-responsive table-responsive-force mb-3">
  <table class="table table-striped table-condensed table-sortable mb-0">
    <thead>
      <tr>
        <th></th>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag('th', Html::encode($lang['name']), [
          'class' => $lang->htmlClasses,
          'data' => [
            'sort' => 'string',
            'sort-onload' => $i === 0 ? 'yes' : '',
          ],
          'lang' => $lang->lang,
        ]) . "\n" ?>
<?php if ($i === 0) { ?>
        <th data-sort="string"><code>key</code></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Aliases')) ?></th>
<?php } ?>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($stages as $stage) { ?>
      <tr>
        <?= Html::tag('td', Icon::s3SalmonStage($stage)) . "\n" ?>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag(
          'td',
          ApiInfoName::widget([
            'name' => Yii::t('app-map3', $stage->name, [], $lang->lang),
            'enName' => $stage->name,
            'lang' => $lang->lang,
          ]),
          [
            'class' => $lang->htmlClasses,
            'lang' => $lang->lang,
          ]
        ) . "\n" ?>
<?php if ($i === 0) { ?>
        <td><code><?= Html::encode($stage->key) ?></code></td>
        <td>
          <?= implode(', ', array_map(
            function (SalmonMap3Alias $alias): string {
              return Html::tag('code', Html::encode($alias->key));
            },
            ArrayHelper::sort(
              $stage->salmonMap3Aliases,
              fn (SalmonMap3Alias $a, SalmonMap3Alias $b): int => strnatcasecmp($a->key, $b->key),
            )
          )) . "\n" ?>
        </td>
<?php } ?>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
