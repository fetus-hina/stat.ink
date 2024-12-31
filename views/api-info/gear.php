<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\Html;

TableResponsiveForceAsset::register($this);

$title = Yii::t('app', 'API Info: Gears: {0}', [
  Yii::t('app-gear', $type->name),
]);
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= implode(' | ', array_map(
      function ($_type) use ($type) : string {
       return ($_type['key'] == $type['key'])
         ? Html::encode(Yii::t('app-gear', $_type['name']))
         : Html::a(
           Html::encode(Yii::t('app-gear', $_type['name'])),
           'gear-' . $_type['key']
         );
      },
      $types
    )) . "\n" ?>
  </p>
  <div class="table-responsive table-responsive-force">
    <table class="table table-striped table-condensed table-sortable">
      <thead>
        <tr>
          <th data-sort="string"><?= Html::encode(Yii::t('app', 'Brand')) ?></th>
          <th data-sort="string"><?= Html::encode(Yii::t('app', 'Primary Ability')) ?></th>
          <th data-sort="string"><code>key</code></th>
<?php foreach ($langs as $lang): ?>
          <th data-sort="string"><?= Html::encode($lang['name']) ?></th>
<?php endforeach ?>
        </tr>
      </thead>
      <tbody>
<?php foreach ($gears as $_gear): ?>
        <tr>
          <?= Html::tag('td', Html::encode($_gear['brand']), ['data-sort-value' => $_gear['brand']]) . "\n" ?>
          <?= Html::tag('td', Html::encode($_gear['ability']), ['data-sort-value' => $_gear['ability']]) . "\n" ?>
          <?= Html::tag('td',
            Html::tag('code', Html::encode($_gear['key'])),
            ['data-sort-value' => $_gear['key']]
          ) . "\n" ?>
<?php foreach ($langs as $lang): ?>
          <?= Html::tag('td', Html::encode($_gear['names'][str_replace('-', '_', $lang['lang'])])) . "\n" ?>
<?php endforeach ?>
        </tr>
<?php endforeach ?>
      </tbody>
    </table>
  </div>
  <hr>
  <p>
    <img src="/static-assets/cc/cc-by.svg" alt="CC-BY 4.0"><br>
    <?= Yii::t(
      'app',
      'This document is under a <a href="http://creativecommons.org/licenses/by/4.0/deed.en">Creative Commons Attribution 4.0 International License</a>.'
    ) . "\n" ?>
  </p>
</div>
