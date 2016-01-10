{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Login'|translate:'app'|escape}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
        <h1>{{'Login'|translate:'app'|escape}}</h1>
        {{ActiveForm assign="_" id="login-form" action=['user/login'] options=['class' => 'form-horizontal']}}
          {{$_->field($login, 'screen_name')}}
          {{$_->field($login, 'password')->passwordInput()}}
          {{Html::submitButton(
              Yii::t('app', 'Login'),
              ['class' => 'btn btn-lg btn-primary btn-block']
            )}}
        {{/ActiveForm}}
        <div style="margin-top:15px">
          {{Html::a(
              Yii::t('app', 'Register'),
              ['user/register'],
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
