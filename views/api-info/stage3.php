<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\helpers\WeaponShortener;
use app\components\widgets\AdWidget;
use app\components\widgets\CcBy;
use app\components\widgets\SnsWidget;
use app\models\Map3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

TableResponsiveForceAsset::register($this);

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Stages (Splatoon 3)');

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

SortableTableAsset::register($this);

$fmt = Yii::$app->formatter;
?>
<div class="container">
  <h1>
    <?= Html::encode($this->title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
  <p>
    <?= implode(' ', [
      Html::a(
        implode('', [
          Html::tag('span', '', ['class' => ['fas fa-file-code fa-fw']]),
          Html::encode(Yii::t('app', 'JSON format')),
        ]),
        ['api-v3/stage'],
        ['class' => 'label label-default']
      ),
      Html::a(
        implode('', [
          Html::tag('span', '', ['class' => ['fas fa-file-code fa-fw']]),
          Html::encode(Yii::t('app', 'JSON format (All langs)')),
        ]),
        ['api-v3/stage', 'full' => 1],
        ['class' => 'label label-default']
      ),
    ]) . "\n" ?>
  </p>
  <div class="table-responsive table-responsive-force">
    <table class="table table-striped table-condensed table-sortable">
      <thead>
        <tr>
<?php foreach ($langs as $i => $lang) { ?>
          <?= Html::tag('th', Html::encode($lang['name']), [
            'data-sort' => 'string',
            'lang' => $lang->lang,
          ]) . "\n" ?>
<?php if ($i === 0) { ?>
          <th data-sort="string"><code>key</code></th>
          <th data-sort="string"><?= Html::encode(Yii::t('app', 'Aliases')) ?></th>
          <th data-sort="int"><?= Html::encode(Yii::t('app', 'Area')) ?></th>
          <th data-sort="int"><?= Html::encode(Yii::t('app', 'Released')) ?></th>
<?php } ?>
<?php } ?>
        </tr>
      </thead>
      <tbody>
<?php foreach ($stages as $stage) { ?>
        <tr>
<?php foreach ($langs as $i => $lang) { ?>
          <?= Html::tag(
            'td',
            Html::encode(Yii::t('app-map3', $stage->name, [], $lang->lang)),
            ['lang' => $lang->lang]
          ) . "\n" ?>
<?php if ($i === 0) { ?>
          <td><code><?= Html::encode($stage->key) ?></code></td>
          <td>
            <?= implode(', ', array_map(
              function (Map3Alias $alias): string {
                return Html::tag('code', Html::encode($alias->key));
              },
              ArrayHelper::sort($stage->map3Aliases, function (Map3Alias $a, Map3Alias $b): int {
                return strnatcasecmp($a->key, $b->key);
              }),
            )) . "\n" ?>
          </td>
          <?= Html::tag(
            'td',
            $stage->area === null ? '' : $fmt->asInteger($stage->area),
            ['data' => [
              'sort-value' => $stage->area === null ? -1 : (int)$stage->area,
            ]]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            $stage->release_at === null
              ? ''
              : Html::tag(
                'time',
                Html::encode($fmt->asDateTime($stage->release_at)),
                ['datetime' => gmdate(DateTime::ATOM, strtotime($stage->release_at))]
              ),
            ['data' => [
              'sort-value' => $stage->release_at === null ? -1 : strtotime($stage->release_at),
            ]]
          ) . "\n" ?>
<?php } ?>
<?php } ?>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
  <hr>
  <?= CcBy::widget() . "\n" ?>
</div>
