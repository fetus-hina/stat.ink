<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\helpers\WeaponShortener;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 */

TableResponsiveForceAsset::register($this);

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Stages (Splatoon 2)');

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
    <?= Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format')),
      ]),
      ['api-v2/stage'],
      ['class' => 'label label-default']
    ) ."\n" ?>
  </p>
  <div class="table-responsive table-responsive-force">
    <table class="table table-striped table-condensed table-sortable">
      <thead>
        <tr>
<?php foreach ($langs as $i => $lang): ?>
          <?= Html::tag('th', Html::encode($lang['name']), [
            'data-sort' => 'string',
            'lang' => $lang->lang,
          ]) . "\n" ?>
<?php if ($i === 0): ?>
          <th data-sort="string"><code>key</code></th>
          <th data-sort="int"><?= Html::encode(Yii::t('app', 'SplatNet 2')) ?></th>
          <th data-sort="int"><?= Html::encode(Yii::t('app', 'Area')) ?></th>
          <th data-sort="int"><?= Html::encode(Yii::t('app', 'Released')) ?></th>
<?php endif ?>
<?php endforeach ?>
        </tr>
      </thead>
      <tbody>
<?php foreach ($stages as $stage): ?>
        <tr>
<?php foreach ($langs as $i => $lang): ?>
          <?= Html::tag(
            'td',
            Html::encode(Yii::t('app-map2', $stage->name, [], $lang->lang)),
            ['lang' => $lang->lang]
          ) . "\n" ?>
<?php if ($i === 0): ?>
          <td><code><?= Html::encode($stage->key) ?></code></td>
          <?= Html::tag(
            'td',
            $stage->splatnet === null
              ? ''
              : Html::tag('code', Html::encode((string)(int)$stage->splatnet)),
            ['data' => [
              'sort-value' => $stage->splatnet === null ? '-1' : (string)(int)$stage->splatnet,
            ]]
          ) . "\n" ?>
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
<?php endif ?>
<?php endforeach ?>
        </tr>
<?php endforeach ?>
      </tbody>
    </table>
  </div>
  <hr>
  <p>
    <img src="/static-assets/cc/cc-by.svg" alt="CC-BY 4.0"><br>
    <?= Yii::t('app', 'This document is under a <a href="http://creativecommons.org/licenses/by/4.0/deed.en">Creative Commons Attribution 4.0 International License</a>.') . "\n" ?>
  </p>
</div>
