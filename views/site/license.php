<?php
use yii\helpers\Html;

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Open Source Licenses'),
]);

$this->context->layout = 'main';
$this->title = $title;
?>
<div class="container">
  <h1 class="ikamodoki">
    <?= Html::encode(Yii::t('app', 'Open Source Licenses')) . "\n" ?>
  </h1>
  <div>
    <h2 class="ikamodoki">
      <?= Html::encode($myself->name) . "\n" ?>
    </h2>
    <div class="license-body">
      <?= $myself->html . "\n" ?>
    </div>
  </div>
  <hr>
<?php foreach ($depends as $software) { ?>
  <div>
    <h2 class="ikamodoki">
      <?= Html::encode($software->name) . "\n" ?>
    </h2>
    <div class="license-body">
      <?= $software->html . "\n" ?>
    </div>
  </div>
<?php } ?>
</div>
