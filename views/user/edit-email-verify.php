<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Alert;
use app\models\EmailVerifyForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var EmailVerifyForm $form
 * @var View $this
 */

$title = Yii::t('app', 'Update Your Email Address');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= Alert::widget([
    'options' => [
      'class' => 'alert-info',
    ],
    'body' => implode('<br>', [
      Html::encode(Yii::t('app', 'Sent an email to your email address. Please check your mailbox and get the verification code.')),
      Html::encode(Yii::t('app', 'Do not close this window.')),
    ]),
  ]) . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-6">
      <?php $_ = ActiveForm::begin(['id' => 'update-form', 'action' => ['edit-email-verify']]); echo "\n" ?>
        <?= $_->field($form, 'email')
          ->label(false)
          ->hiddenInput() . "\n"
        ?>
        <?= $_->field($form, 'verifyCode')
          ->textInput([
            'placeholder' => 'xxxxx',
            'pattern' => '^[a-z0-7]{5}$',
          ]) . "\n" ?>

        <?= Html::submitButton(
          Html::encode(Yii::t('app', 'Verify')),
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
  </div>
</div>
