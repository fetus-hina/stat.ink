<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\PasswordStrength;
use app\models\ResetPasswordRecoveryKeyForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var ResetPasswordRecoveryKeyForm $form
 * @var View $this
 * @var string $cfToken
 */

$this->title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app-user', 'Reset your password'),
]);

$this->registerJsFile(
  'https://challenges.cloudflare.com/turnstile/v0/api.js',
  [
    'async' => true,
    'defer' => true,
    'position' => View::POS_END,
  ],
);

?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6 mb-3">
      <h1 class="mb-3">
        <?= Html::encode(Yii::t('app-user', 'Reset your password')) . "\n" ?>
      </h1>
      <p class="mb-3">
        <?= Html::a(
          implode(' ', [
            Icon::back(),
            Yii::t('app', 'Back'),
          ]),
          ['user/login'],
          ['class' => 'btn btn-default'],
        ) . "\n" ?>
      </p>
      <div class="panel panel-default">
        <div class="panel-body">
          <?php $_ = ActiveForm::begin(['id' => 'form']); echo "\n" ?>
            <?= $_->field($form, 'screen_name')
              ->textInput([
                'autocomplete' => 'username',
              ])
              ->hint(Yii::t('app-user', '<code>@id</code> (without <code>@</code>), case sensitive.'))
              . "\n"
            ?>
            <?= $_->field($form, 'recovery_key')
              ->passwordInput([
                'autocomplete' => 'off',
              ]) . "\n" ?>
            <hr>
            <?= $_->field($form, 'password')
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
            <?= $_->field($form, 'password_repeat')
              ->passwordInput([
                'autocomplete' => 'new-password',
                'minlength' => 10,
              ]) . "\n" ?>
            <?= PasswordStrength::widget([
              'selector' => '#' . Html::getInputId($form, 'password'),
            ]) . "\n" ?>
            <hr>
            <?= Html::tag('div', '', [
              'class' => 'cf-turnstile',
              'data' => [
                'action' => 'reset-password-recovery-key',
                'language' => Yii::$app->language,
                'sitekey' => $cfToken,
                'theme' => Yii::$app->theme->isDarkTheme ? 'dark' : 'light',
              ],
            ]) . "\n" ?>
            <hr>
            <?= Html::submitButton(
              Html::encode(Yii::t('app-user', 'Change Password')),
              ['class' => 'btn btn-primary btn-block']
            ) . "\n" ?>
          <?php ActiveForm::end(); echo "\n" ?>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 mb-3" style="padding:0 5%">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
