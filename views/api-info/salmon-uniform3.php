<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\ApiInfoName;
use app\components\widgets\CcBy;
use app\components\widgets\SnsWidget;
use app\models\Language;
use app\models\SalmonUniform3;
use app\models\SalmonUniform3Alias;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var SalmonUniform3[] $uniforms
 * @var View $this
 */

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Uniforms (Splatoon 3)');

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<div class="container">
  <h1><?= Html::encode($this->title) ?></h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2><?= Html::encode(Yii::t('app', 'Uniform')) ?></h2>
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
<?php foreach ($uniforms as $uniform) { ?>
        <tr>
          <?= Html::tag(
            'td',
            Html::tag('code', Html::encode($uniform->key)),
            [
              'data' => [
                'sort-value' => $uniform->key,
              ],
            ]
          ) . "\n" ?>
          <?= Html::tag(
            'td',
            implode(', ', array_map(
              fn (SalmonUniform3Alias $alias): string => Html::tag('code', Html::encode($alias->key)),
              ArrayHelper::sort(
                $uniform->salmonUniform3Aliases,
                fn (SalmonUniform3Alias $a, SalmonUniform3Alias $b): int => strcmp($a->key, $b->key),
              ),
            )),
          ) . "\n" ?>
<?php foreach ($langs as $j => $lang) { ?>
          <?= Html::tag(
            'td',
            ApiInfoName::widget([
              'name' => Yii::t('app-salmon-uniform3', $uniform->name, [], $lang->lang),
              'enName' => $uniform->name,
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
