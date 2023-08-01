<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\models\ResetPasswordApikeyForm;
use jp3cki\yii2\zxcvbn\ZxcvbnAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var ResetPasswordApikeyForm $form
 * @var View $this
 * @var string $cfToken
 */

$this->title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Reset your password'),
]);

ZxcvbnAsset::register($this);

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
        <?= Html::encode(Yii::t('app', 'Reset your password')) . "\n" ?>
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
              ->hint(Yii::t('app', '<code>@id</code> (without <code>@</code>), case sensitive.'))
              . "\n"
            ?>
            <?= $_->field($form, 'api_key')
              ->passwordInput([
                'autocomplete' => 'off',
              ]) . "\n" ?>
            <hr>
            <?= $_->field($form, 'password')
              ->passwordInput([
                'autocomplete' => 'new-password',
              ]) . "\n" ?>
            <?= $_->field($form, 'password_repeat')
              ->passwordInput() . "\n" ?>
            <div id="password-strength"></div>
            <hr>
            <?= Html::tag('div', '', [
              'class' => 'cf-turnstile',
              'data' => [
                'action' => 'reset-password-apikey',
                'language' => Yii::$app->language,
                'sitekey' => $cfToken,
                'theme' => Yii::$app->theme->isDarkTheme ? 'dark' : 'light',
              ],
            ]) . "\n" ?>
            <hr>
            <?= Html::submitButton(
              Html::encode(Yii::t('app', 'Change Password')),
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
<?php
$this->registerJs(<<<'EOF'
(function ($) {
  "use strict";
  var $container = $('#password-strength');
  $container.empty().append($('<div>', {id: 'password-strength-gauge'}).width(0));

  var $input = $('input[name="ResetPasswordApikeyForm[password]"]');
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
