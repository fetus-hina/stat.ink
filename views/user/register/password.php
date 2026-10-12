<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\user\RegisterAction;
use app\components\widgets\Icon;
use app\models\RegisterForm;
use jp3cki\yii2\zxcvbn\ZxcvbnAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var RegisterForm $register
 * @var View $this
 */

ZxcvbnAsset::register($this);

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
  <div id="password-strength"></div>
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
<?php
$this->registerJs(<<<'EOF'
(function ($) {
  "use strict";
  var $container = $('#password-strength');
  $container.empty().append($('<div>', {id: 'password-strength-gauge'}).width(0));

  var $input = $('input[name="RegisterForm[password]"]');
  var timerId = null;
  var doUpdate = function () {
    timerId = null;
    var score = zxcvbn($input.val() + "").score;
    $('#password-strength-gauge').width((1 + (99 * score / 4)) + '%');
  };

  $input.keydown(function () {
    if (timerId !== null) {
      window.clearTimeout(timerId);
      timerId = null;
    }
    timerId = window.setTimeout(doUpdate, 100);
  });

  window.setTimeout(doUpdate, 1);
})(jQuery);
EOF
);
$this->registerCss(<<<'EOF'
#password-strength {
  box-sizing:border-box;
  font-size:1px;
  line-height:1;
  width:100%;
  height:6px;
  border:1px solid #ccc;
  border-radius:3px;
  margin-bottom: 15px;
}

#password-strength-gauge {
  width:0;
  height:100%;
  background-color:#0d0;
}
EOF
);
