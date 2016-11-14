{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Login'|translate:'app'|escape}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">
              {{'Login'|translate:'app'|escape}}
            </h1>
          </div>
          <div class="panel-body">
            {{ActiveForm assign="_" id="login-form" action=['user/login']}}
              {{$_->field($login, 'screen_name')}}
              {{$_->field($login, 'password')->passwordInput()}}
              <div class="form-group">
                {{Html::submitButton(
                    Yii::t('app', 'Login'),
                    ['class' => 'btn btn-primary btn-block']
                  )}}
              </div>
              <div class="form-group">
                {{Html::a(
                    Yii::t('app', 'Register'),
                    ['user/register'],
                    ['class' => 'btn btn-default btn-block']
                  )}}
              </div>
            {{/ActiveForm}}
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h2 class="panel-title">
              {{'Log in with other services'|translate:'app'|escape}}
            </h2>
          </div>
          <div class="panel-body">
            <div class="form-group">
              {{$_provided = false}}
              {{if $app->params['twitter']['read_enabled']}}
                <a href="{{url route='user/login-with-twitter'}}" class="btn btn-info btn-block">
                  <span class="fa fa-twitter left"></span>
                  {{'Log in with Twitter'|translate:'app'|escape}}
                </a>
                {{$_provided = true}}
              {{/if}}

              {{if !$_provided}}
                <p>
                  {{'No service configured by the system administrator.'|translate:'app'|escape}}
                </p>
              {{/if}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        {{AdWidget}}
      </div>
    </div>
  </div>
{{/strip}}
