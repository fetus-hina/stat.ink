{{strip}}
  {{\app\assets\ZxcvbnAsset::register($this)|@void}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Log In'|translate:'app'|escape}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  {{use class="himiklab\yii2\recaptcha\ReCaptcha"}}
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        <h1>{{'Log In'|translate:'app'|escape}}</h1>
        {{ActiveForm assign="_" id="login-form" action=['user/login'] options=['class' => 'form-horizontal']}}
          {{$_->field($login, 'screen_name')}}
          {{$_->field($login, 'password')->passwordInput()}}
          {{Html::submitButton(
              Yii::t('app', 'Log In'),
              ['class' => 'btn btn-lg btn-primary btn-block']
            )}}
        {{/ActiveForm}}
      </div>
      <hr class="visible-xs">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        <h1>{{'Register'|translate:'app'|escape}}</h1>
        {{ActiveForm assign="_" id="register-form" action=['user/register'] options=['class' => 'form-horizontal']}}
          {{$_->field($register, 'name')}}
          {{$_->field($register, 'screen_name')}}
          {{$_->field($register, 'password')->passwordInput()}}
          <div id="password-strength"><!--TODO--></div>
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
      </div>
    </div>
  </div>
{{/strip}}
