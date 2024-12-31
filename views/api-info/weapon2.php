<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\Spl2WeaponAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\helpers\WeaponShortener;
use app\components\widgets\AbilityIcon;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 */

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Weapons (Splatoon 2)');

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

$shortener = Yii::createObject(['class' => WeaponShortener::class]);
$icon = Spl2WeaponAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($this->title) ?></h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
  <p>
    <?= Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format')),
      ]),
      ['api-v2/weapon'],
      ['class' => 'label label-default']
    ) ."\n" ?>
    <?= Html::a(
      implode(' ', [
        Icon::fileCsv(),
        Html::encode(Yii::t('app', 'CSV format')),
      ]),
      ['api-v2/weapon', 'format' => 'csv'],
      ['class' => 'label label-default']
    ) ."\n" ?>
  </p>
  <div class="table-responsive table-responsive-force">
    <table class="table table-striped table-condensed table-sortable">
      <thead>
        <tr>
          <th data-sort="int">
            <?= Html::encode(Yii::t('app', 'Category') . ' 1') . "\n" ?>
          </th>
          <th data-sort="int">
            <?= Html::encode(Yii::t('app', 'Category') . ' 2') . "\n" ?>
          </th>
          <th data-sort="string">
            <code>key</code>
          </th>
          <th data-sort="int">
            <?= Html::encode(Yii::t('app', 'SplatNet 2')) . "\n" ?>
          </th>
          <th></th>
<?php foreach ($langs as $i => $lang) { ?>
          <th data-sort="string">
            <?= Html::encode($lang['name']) . "\n" ?>
          </th>
<?php if ($i === 0) { ?>
          <th data-sort="string">
            <?= Html::encode(Yii::t('app', 'Weapon (Short)')) . "\n" ?>
          </th>
          <th data-sort="string">
            <?= Html::encode(Yii::t('app', 'Sub Weapon')) . "\n" ?>
          </th>
          <th data-sort="string">
            <?= Html::encode(Yii::t('app', 'Special')) . "\n" ?>
          </th>
          <th data-sort="string"><?=
            Html::encode(Yii::t('app', 'Main Weapon'))
          ?></th>
          <th data-sort="string">
            <?= Html::encode(Yii::t('app', 'Reskin of')) . "\n" ?>
          </th>
          <th data-sort="string"><?= implode(' ', [
            AbilityIcon::spl2('main_power_up', ['style' => [
              'height' => '1.333em',
            ]]),
            Html::encode(Yii::t('app-ability2', 'Main Power Up')),
          ]) ?></th>
<?php } ?>
<?php } ?>
        </tr>
      </thead>
      <tbody>
<?php $i = 0; ?>
<?php foreach ($categories as $category) { ?>
<?php foreach ($category['types'] as $type) { ?>
<?php foreach ($type['weapons'] as $weapon) { ?>
<?php ++$i; ?>
        <tr>
          <td data-sort-value="<?= Html::encode((string)$i) ?>">
            <?= Html::encode($category['name']) . "\n" ?>
          </td>
          <td data-sort-value="<?= Html::encode((string)$i) ?>">
            <?= Html::encode($type['name']) . "\n" ?>
          </td>
          <td data-sort-value="<?= Html::encode($weapon['key']) ?>">
            <code><?= Html::encode($weapon['key']) ?></code>
          </td>
          <?= Html::tag(
            'td',
            $weapon['splatnet'] === null
              ? ''
              : Html::tag('code', Html::encode($weapon['splatnet'])),
            [
              'data' => [
                'sort-value' => $weapon['splatnet'] === null
                  ? PHP_INT_MAX
                  : $weapon['splatnet'],
              ],
              'class' => [
                'text-right',
              ],
            ]
          ) . "\n" ?>
          <td><?= Html::img($icon->getIconUrl($weapon['key']), [
            'style' => [
              'height' => '1.333em',
            ],
          ]) ?></td>
<?php foreach ($langs as $j => $lang) { ?>
<?php $name = $weapon['names'][str_replace('-', '_', $lang['lang'])] ?? '' ?>
          <?= Html::tag('td', Html::encode($name), [
            'data' => [
              'sort-value' => $name,
            ],
          ]) . "\n" ?>
<?php if ($j === 0) { ?>
          <td>
<?php $short = $shortener->get($name) ?>
<?php if ($short != '' && $short !== $name) { ?>
            <?= Html::encode($shortener->get($name)) . "\n" ?>
<?php } ?>
          </td>
          <?= Html::tag(
            'td',
            implode(' ', [
              Html::img($icon->getIconUrl('sub/' . $weapon['subKey']), [
                'style' => [
                  'height' => '1.333em',
                ],
              ]),
              Html::encode($weapon['sub']),
            ]),
            ['data-sort-value' => $weapon['sub']]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            implode(' ', [
              Html::img($icon->getIconUrl('sp/' . $weapon['specialKey']), [
                'style' => [
                  'height' => '1.333em',
                ],
              ]),
              Html::encode($weapon['special']),
            ]),
            ['data-sort-value' => $weapon['special']]
          ) . "\n" ?>
          <td data-sort-value="<?= Html::encode($weapon['mainReference']) ?>"><?= implode(' ', [
            Html::img($icon->getIconUrl($weapon['mainReferenceKey']), [
              'style' => [
                'height' => '1.333em',
              ],
            ]),
            Html::encode($weapon['mainReference']),
          ]) ?></td>
          </td>
          <td data-sort-value="<?= Html::encode($weapon['canonical']) ?>"><?= implode(' ', [
            Html::img($icon->getIconUrl($weapon['canonicalKey']), [
              'style' => [
                'height' => '1.333em',
              ],
            ]),
            Html::encode($weapon['canonical']),
          ]) ?></td>
          <td data-sort-value="<?= Html::encode($weapon['mainPowerUp']) ?>">
            <?= Html::encode($weapon['mainPowerUp']) . "\n" ?>
          </td>
<?php } ?>
<?php } ?>
        </tr>
<?php } ?>
<?php } ?>
<?php } ?>
      </tbody>
    </table>
  </div>
  <hr>
  <p>
    <img src="/static-assets/cc/cc-by.svg" alt="CC-BY 4.0"><br>
    <?= Yii::t('app', 'This document is under a <a href="http://creativecommons.org/licenses/by/4.0/deed.en">Creative Commons Attribution 4.0 International License</a>.') . "\n" ?>
  </p>
</div>
