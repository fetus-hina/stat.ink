<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EntireWeaponsUseAsset;
use app\assets\GraphIconAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\WeaponCompareForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 */

$this->context->layout = 'main';

$title = Yii::t('app', 'Weapons');
$subTitle = Yii::t('app', 'Compare Number Of Uses');
$this->title = vsprintf('%s | %s - %s', [
    Yii::$app->name,
    $subTitle,
    $title,
]);

$canonicalUrl = Url::to(['entire/weapons-use', 'cmp' => $form->toQueryParams('')], true);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $canonicalUrl]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $canonicalUrl]);

EntireWeaponsUseAsset::register($this);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2><?= Html::encode(Yii::t('app', 'Compare Number Of Uses')) ?></h2>
  <div id="graph-trends-legends"></div>
  <?= Html::tag('div', '', [
    'id' => 'graph-trends',
    'class' => 'graph',
    'data' => [
      'refs' => 'trends-data',
      'legends' => 'graph-trends-legends',
      'icon' => Yii::$app->assetManager->getAssetUrl(
        GraphIconAsset::register($this),
        'dummy.png'
      ),
    ],
  ]) . "\n" ?>
  <p class="text-right"><?= Html::tag('label', implode(' ', [
    Html::checkbox('stack-trends', false, ['value' => '1', 'id' => 'stack-trends']),
    Html::encode(Yii::t('app', 'Stack')),
  ])) ?></p>
  <?php $_form = ActiveForm::begin(['method' => 'GET', 'id' => 'compare-form']); echo "\n" ?>
    <div class="form-group"><?= Html::submitButton(
      Html::encode(Yii::t('app', 'Update')),
      ['class' => 'btn btn-primary']
    ) ?></div>
    <div class="row">
      <div class="col-xs-12 col-sm-8 col-lg-6">
<?php foreach (range(1, WeaponCompareForm::NUMBER) as $i) { ?>
        <div class="row">
          <div class="col-xs-6">
            <?= $_form->field($form, "weapon{$i}")->label(false)->dropDownList($weapons) . "\n" ?>
          </div>
          <div class="col-xs-6">
            <?= $_form->field($form, "rule{$i}")->label(false)->dropDownList($rules) . "\n" ?>
          </div>
        </div>
<?php } ?>
      </div>
    </div>
    <div class="form-group"><?= Html::submitButton(
      Html::encode(Yii::t('app', 'Update')),
      ['class' => 'btn btn-primary']
    ) ?></div>
  <?php ActiveForm::end(); echo "\n" ?>
  <?= Html::tag('script', Json::encode($data), [
    'id' => 'trends-data',
    'type' => 'application/json',
  ]) . "\n" ?>
</div>
