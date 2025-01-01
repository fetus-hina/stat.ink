<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
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

$this->title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Register'),
]);

ZxcvbnAsset::register($this);

?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
      <h1>
        <?= Html::encode(Yii::t('app', 'Register')) . "\n" ?>
      </h1>
      <?= Html::a(
        Yii::t('app', 'If you already have an account, please click here.'),
        ['user/login'],
        []
      ) . "\n" ?>
      <p>
        <?= Html::encode(Yii::t('app', 'The password will be encrypted.')) . "\n" ?>
        <?= Html::a(
          Icon::help(),
          'https://github.com/fetus-hina/stat.ink/wiki/Store-Your-Password',
          ['rel' => 'external']
        ) . "\n" ?>
      </p>
      <?php $_ = ActiveForm::begin(['id' => 'register-form']); echo "\n" ?>
        <?= $_->field($register, 'name')
          ->textInput(['autocomplete' => 'nickname']) . "\n"
        ?>
        <?= $_->field($register, 'screen_name')
          ->textInput(['autocomplete' => 'username'])
          ->hint(Yii::t('app', 'This will be made public as part of URL')) . "\n"
        ?>
        <?= $_->field($register, 'password')
          ->passwordInput([
            'autocomplete' => 'new-password'
          ])
          ->hint(
            Yii::t(
              'app',
              'This should be a random string of at least {n} characters and should not be the same as any other site',
              ['n' => 10],
            )
          ) . "\n"
        ?>
        <?= $_->field($register, 'password_repeat')->passwordInput() . "\n" ?>
        <div id="password-strength"></div>
        <?= Html::submitButton(
          Html::encode(Yii::t('app', 'Register')),
          ['class' => 'btn btn-primary btn-block']
        ) . "\n" ?>
      <?php ActiveForm::end(); echo "\n" ?>
      <?= Html::tag(
        'div',
        Html::a(
          Html::encode(Yii::t('app', 'Login')),
          ['/user/login'],
          ['class' => 'btn btn-default btn-block']
        ),
        ['style' => [
          'margin-top' => '15px',
        ]]
      ) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
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
