<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\models\EmailForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var EmailForm $form
 * @var View $this
 */

$title = Yii::t('app', 'Update Your Email Address');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);
?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6" style="padding:0 5%">
      <h1><?= Html::encode($title) ?></h1>
      
      <?php $_ = ActiveForm::begin(['id' => 'update-form', 'action' => ['edit-email']]); echo "\n" ?>
        <?= $_->field($form, 'email')
          ->hint(Yii::t('app', 'You can\'t use an IDN (Internationalized Domain Names) email address'))
          ->textInput(['type' => 'email']) . "\n" ?>

        <?= Html::submitButton(
          Html::encode(Yii::t('app', 'Update')),
          ['class' => 'btn btn-lg btn-primary btn-block']
        ) . "\n" ?>
      <?php ActiveForm::end(); echo "\n" ?>

      <div style="margin-top:15px">
        <?= Html::a(
          Yii::t('app', 'Back'),
          ['profile'],
          ['class' => 'btn btn-lg btn-default btn-block']
        ) . "\n" ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6" style="padding:0 5%">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
