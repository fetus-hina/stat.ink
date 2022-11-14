<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\ApiInfoName;
use app\components\widgets\CcBy;
use app\components\widgets\SnsWidget;
use app\models\Language;
use app\models\SalmonEvent3;
use app\models\SalmonEvent3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var SalmonEvent3[] $events
 * @var View $this
 */

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Known Occurrences (Splatoon 3)');

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<div class="container">
  <h1><?= Html::encode($this->title) ?></h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
<?php if (false) { ?>
  <p>
    <?= implode(' ', [
      Html::a(
        implode('', [
          Html::tag('span', '', ['class' => ['fas fa-file-code fa-fw']]),
          Html::encode(Yii::t('app', 'JSON format')),
        ]),
        ['api-v3/weapon'],
        ['class' => 'label label-default']
      ),
      Html::a(
        implode('', [
          Html::tag('span', '', ['class' => ['fas fa-file-code fa-fw']]),
          Html::encode(Yii::t('app', 'JSON format (All langs)')),
        ]),
        ['api-v3/weapon', 'full' => 1],
        ['class' => 'label label-default']
      ),
    ]) . "\n" ?>
  </p>
<?php } ?>

  <h2><?= Html::encode(Yii::t('app-salmon3', 'Known Occurrence')) ?></h2>
  <div class="table-responsive table-responsive-force">
    <table class="table table-striped table-condensed table-sortable">
      <thead>
        <tr>
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
<?php foreach ($events as $event) { ?>
        <tr>
          <?= Html::tag(
            'td',
            Html::tag('code', Html::encode($event->key)),
            [
              'data' => [
                'sort-value' => $event->key,
              ],
            ]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            implode(', ', array_map(
              fn (SalmonEvent3Alias $alias): string => Html::tag('code', Html::encode($alias->key)),
              ArrayHelper::sort(
                $event->salmonEvent3Aliases,
                fn (SalmonEvent3Alias $a, SalmonEvent3Alias $b): int => strcmp($a->key, $b->key),
              ),
            )),
          ) . "\n" ?>
<?php foreach ($langs as $j => $lang) { ?>
          <?= Html::tag(
            'td',
            ApiInfoName::widget([
              'name' => Yii::t('app-salmon-event3', $event->name, [], $lang->lang),
              'enName' => $event->name,
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
  <hr>
  <?= CcBy::widget() . "\n" ?>
</div>
