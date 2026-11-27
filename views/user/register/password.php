<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\user\RegisterAction;
use app\components\widgets\Icon;
use app\components\widgets\PasswordStrength;
use app\models\RegisterForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var RegisterForm $register
 * @var View $this
 */

?>
<p>
  <?= Html::encode(Yii::t('app-user', 'The password will be encrypted.')) . "\n" ?>
  <?= Html::a(
    Icon::help(),
    'https://github.com/fetus-hina/stat.ink/wiki/Store-Your-Password',
    ['rel' => 'external']
  ) . "\n" ?>
</p>
<?php $_ = ActiveForm::begin([
  'id' => 'register-form',
  'action' => ['user/register', 'method' => RegisterAction::METHOD_PASSWORD],
]); echo "\n" ?>
  <?= $_->field($register, 'name')
    ->textInput(['autocomplete' => 'nickname']) . "\n"
  ?>
  <?= $_->field($register, 'screen_name')
    ->textInput(['autocomplete' => 'username'])
    ->hint(Yii::t('app-user', 'This will be made public as part of URL')) . "\n"
  ?>
  <?= $_->field($register, 'password')
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
      )
    ) . "\n"
  ?>
  <?= $_->field($register, 'password_repeat')->passwordInput([
    'autocomplete' => 'new-password',
    'minlength' => 10,
  ]) . "\n" ?>
  <?= PasswordStrength::widget([
    'selector' => '#' . Html::getInputId($register, 'password'),
  ]) . "\n" ?>
  <?= Html::submitButton(
    Html::encode(Yii::t('app-user', 'Register')),
    ['class' => 'btn btn-primary btn-block']
  ) . "\n" ?>
<?php ActiveForm::end(); echo "\n" ?>
<p class="text-center" style="margin-top:15px">
  <?= Html::a(
    implode(' ', [
      Icon::passkey(),
      Html::encode(Yii::t('app-user', 'Register with a passkey instead')),
    ]),
    ['user/register'],
  ) . "\n" ?>
</p>
