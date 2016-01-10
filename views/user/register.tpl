{{strip}}
  {{\jp3cki\yii2\zxcvbn\ZxcvbnAsset::register($this)|@void}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Register'|translate:'app'|escape}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  {{use class="himiklab\yii2\recaptcha\ReCaptcha"}}
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        <h1>{{'Register'|translate:'app'|escape}}</h1>
        {{Html::a(
            Yii::t('app', 'If you already have an account, please click here.'),
            ['user/login'],
            []
          )}}
        <p>{{'The password will be encrypted.'|translate:'app'|escape}}</p>
        {{ActiveForm assign="_" id="register-form" action=['user/register']}}
          {{$_->field($register, 'name')}}
          {{$_->field($register, 'screen_name')}}
          {{$_->field($register, 'password')->passwordInput()}}
          {{$_->field($register, 'password_repeat')->passwordInput()}}
          <div id="password-strength"></div>
          {{if $app->params.googleRecaptcha.siteKey != ''}}
            <div class="form-group">
              {{ReCaptcha::widget([
                  'name' => 'recaptcha',
                  'siteKey' => $app->params.googleRecaptcha.siteKey,
                  'secret' => $app->params.googleRecaptcha.secret
              ])}}
            </div>
          {{/if}}
          {{Html::submitButton(
              Yii::t('app', 'Register'),
              ['class' => 'btn btn-lg btn-primary btn-block']
            )}}
        {{/ActiveForm}}
        <div style="margin-top:15px">
          {{Html::a(
              Yii::t('app', 'Login'),
              ['user/login'],
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
{{registerJs}}{{literal}}
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
{{/literal}}{{/registerJs}}
{{registerCss}}{{literal}}
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
{{/literal}}{{/registerCss}}
