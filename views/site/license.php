<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var object $myself
 * @var object[] $depends
 */

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Open Source Licenses'),
]);

$this->context->layout = 'main';
$this->title = $title;

?>
<div class="container">
  <h1><?= Html::encode(Yii::t('app', 'Open Source Licenses')) ?></h1>
  <div>
    <h2>
      <?= Html::encode($myself->name) . "\n" ?>
    </h2>
    <div class="license-body">
      <?= $myself->html . "\n" ?>
    </div>
  </div>
  <hr>
<?php foreach ($depends as $software) { ?>
  <div>
    <h2><?= Html::encode($software->name) ?></h2>
    <div class="license-body">
      <?= $software->html . "\n" ?>
    </div>
  </div>
<?php } ?>
</div>
