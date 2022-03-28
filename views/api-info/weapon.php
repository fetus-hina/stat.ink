<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\helpers\Html;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\web\View;
use app\models\Language;

/**
 * @var array[] $langs
 * @var View $this
 * @var array[] $types
 */

TableResponsiveForceAsset::register($this);

$title = Yii::t('app', 'API Info: Weapons');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

SortableTableAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="table-responsive table-responsive-force">
    <table class="table table-striped table-condensed table-sortable">
      <thead>
        <tr>
          <th data-sort="int"><?= Html::encode(Yii::t('app', 'Category')) . "\n" ?></th>
          <th data-sort="string"><code>key</code></th>
          <?= implode('', array_map(
            function ($lang) : string {
              return Html::tag('th', Html::encode($lang['name']), ['data-sort' => 'string']);
            },
            $langs
          )) . "\n" ?>
        </tr>
      </thead>
      <tbody>
<?php foreach ($types as $i => $type): ?>
<?php foreach ($type['weapons'] as $weapon): ?>
        <tr>
          <?= Html::tag(
            'td',
            Html::encode($type['name']),
            ['data-sort-value' => $i]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            Html::tag('code', Html::encode($weapon['key'])),
            ['data-sort-value' => $weapon['key']]
          ) . "\n" ?>
<?php foreach ($langs as $lang): ?>
          <td>
            <?= Html::encode(
              $weapon['names'][str_replace('-', '_', $lang['lang'])]
            ) . "\n" ?>
          </td>
<?php endforeach ?>
        </tr>
<?php endforeach ?>
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
