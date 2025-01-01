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
use app\models\Map3;
use app\models\Map3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Map3[] $stages
 * @var View $this
 * @var array[] $langs
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

$fmt = Yii::$app->formatter;

$launch = new DateTimeImmutable('2022-09-09T00:00:00+00:00');

?>
<div class="table-responsive table-responsive-force mb-3">
  <table class="table table-striped table-condensed table-sortable mb-0">
    <thead>
      <tr>
        <th></th>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag('th', Html::encode($lang['name']), [
          'class' => $lang->htmlClasses,
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
        <td><?= Icon::s3LobbyRegular() ?></td>
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
            function (Map3Alias $alias): string {
              return Html::tag('code', Html::encode($alias->key));
            },
            ArrayHelper::sort(
              $stage->map3Aliases,
              fn (Map3Alias $a, Map3Alias $b): int => strnatcasecmp($a->key, $b->key),
            ),
          )) . "\n" ?>
        </td>
        <?= Html::tag(
          'td',
          $stage->area === null ? '' : $fmt->asInteger($stage->area),
          [
            'data' => [
              'sort-value' => $stage->area === null ? -1 : (int)$stage->area,
            ],
          ],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $stage->release_at === null
            ? ''
            : Html::tag(
              'time',
              (new DateTimeImmutable($stage->release_at)) <= $launch
                ? Html::encode(Yii::t('app', 'Launch'))
                : Html::encode($fmt->asDate($stage->release_at, 'medium')),
              [
                'datetime' => gmdate(DateTime::ATOM, strtotime($stage->release_at)),
              ],
            ),
          [
            'data' => [
              'sort-value' => $stage->release_at === null
                ? '-1'
                : (string)strtotime($stage->release_at),
            ],
          ]
        ) . "\n" ?>
<?php } ?>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
