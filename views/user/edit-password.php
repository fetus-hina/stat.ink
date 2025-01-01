<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\models\PasswordForm;
use jp3cki\yii2\zxcvbn\ZxcvbnAsset;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var PasswordForm $form
 * @var View $this
 */

$title = Yii::t('app', 'Update Your Password');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

ZxcvbnAsset::register($this);
?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
      <h1><?= Html::encode($title) ?></h1>
      
      <?php $_ = ActiveForm::begin(['id' => 'update-form', 'action' => ['edit-password']]); echo "\n" ?>
        <?= $_->field($form, 'password')
          ->passwordInput([
            'autocomplete' => 'current-password',
          ]) . "\n"
        ?>
        <?= $_->field($form, 'new_password')
          ->passwordInput([
            'autocomplete' => 'new-password'
          ])
          ->hint(
            Yii::t(
              'app',
              'This should be a random string of at least {n} characters and should not be the same as any other site',
              ['n' => 10],
            ),
          ) . "\n"
        ?>
        <?= $_->field($form, 'new_password_repeat')->passwordInput() . "\n" ?>
        <div id="password-strength"></div>

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
<?php
$js = <<<'JS'
(function ($) {
  "use strict";
  var $container = $('#password-strength');
  $container.empty().append($('<div>', {id: 'password-strength-gauge'}).width(0));

  var $input = $('input[name="PasswordForm[new_password]"]');
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
JS;
$this->registerJs($js) ?>
<?php $this->registerCss(<<<'CSS'
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
CSS
) ?>
