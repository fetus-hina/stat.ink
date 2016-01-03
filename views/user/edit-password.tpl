{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Update Your Password'|translate:'app'}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        <h1>
          {{'Update Your Password'|translate:'app'|escape}}
        </h1>
        
        {{ActiveForm assign="_" id="update-form" action=['user/edit-password']}}
          {{$_->field($form, 'password')->passwordInput()}}
          {{$_->field($form, 'new_password')->passwordInput()}}
          {{$_->field($form, 'new_password_repeat')->passwordInput()}}
          {{\jp3cki\yii2\zxcvbn\ZxcvbnAsset::register($this)|@void}}
          <div id="password-strength"></div>

          {{Html::submitButton(
              Yii::t('app', 'Update'),
              ['class' => 'btn btn-lg btn-primary btn-block']
            )}}
        {{/ActiveForm}}

        <div style="margin-top:15px">
          {{Html::a(
              Yii::t('app', 'Back'),
              ['user/profile'],
              ['class' => 'btn btn-lg btn-default btn-block']
            )}}
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
{{registerJs}}
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
{{/registerJs}}
{{registerCss}}
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
{{/registerCss}}
