<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\PasswordStrength;
use app\models\PasswordForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var PasswordForm $form
 * @var View $this
 * @var bool $hasPassword
 */

$title = $hasPassword
  ? Yii::t('app-user', 'Update Your Password')
  : Yii::t('app-user', 'Set Your Password');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
      <h1><?= Html::encode($title) ?></h1>
      
      <?php if (!$hasPassword) { ?>
        <p>
          <?= Html::encode(
            Yii::t(
              'app-user',
              'Your password is currently disabled. You can set a new password after verifying with your passkey.',
            ),
          ) . "\n" ?>
        </p>
        <?= $this->render('passkey-reauth/config') . "\n" ?>
      <?php } ?>
      <?php $_ = ActiveForm::begin([
        'id' => 'update-form',
        'action' => ['edit-password'],
        'options' => $hasPassword ? [] : ['data-passkey-reauth' => 'active-form'],
      ]); echo "\n" ?>
        <?php if ($hasPassword) { ?>
          <?= $_->field($form, 'password')
            ->passwordInput([
              'autocomplete' => 'current-password',
            ]) . "\n"
          ?>
        <?php } ?>
        <?= $_->field($form, 'new_password')
          ->passwordInput([
            'autocomplete' => 'new-password',
            'minlength' => 10,
            'passwordrules' => 'minlength: 10;',
          ])
          ->hint(
            Yii::t(
              'app-user',
              'This should be a random string of at least {n} characters and should not be the same as any other site',
              ['n' => 10],
            ),
          ) . "\n"
        ?>
        <?= $_->field($form, 'new_password_repeat')->passwordInput([
          'autocomplete' => 'new-password',
          'minlength' => 10,
        ]) . "\n" ?>
        <?= PasswordStrength::widget([
          'selector' => '#' . Html::getInputId($form, 'new_password'),
        ]) . "\n" ?>

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
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
