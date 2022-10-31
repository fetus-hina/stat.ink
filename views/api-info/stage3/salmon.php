<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\models\SalmonMap3;
use app\models\SalmonMap3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var View $this
 * @var array[] $langs
 */

$stages = ArrayHelper::sort(
    SalmonMap3::find()->with(['salmonMap3Aliases'])->all(),
    fn (SalmonMap3 $a, SalmonMap3 $b): int => strnatcasecmp(
        Yii::t('app-map3', $a->name),
        Yii::t('app-map3', $b->name)
    )
);

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

$fmt = Yii::$app->formatter;

?>
<h2 id="salmon3">
  <?= Html::img(
    Yii::$app->assetManager->getAssetUrl(GameModeIconsAsset::register($this), 'spl3/salmon.png'),
    [
      'style' => [
        'width' => 'auto',
        'height' => '1em',
      ],
    ]
  ) . "\n" ?>
  <?= Html::encode(Yii::t('app-salmon3', 'Salmon Run')) . "\n" ?>
</h2>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag('th', Html::encode($lang['name']), [
          'class' => $lang->htmlClasses,
          'data-sort' => 'string',
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
                fn (SalmonMap3Alias $a, SalmonMap3Alias $b): int => strnatcasecmp($a->key, $b->key)
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
