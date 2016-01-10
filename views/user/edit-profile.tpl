{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Update Your Profile'|translate:'app'}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  <div class="container">
    <h1>
      {{'Update Your Profile'|translate:'app'|escape}}
    </h1>
    
    {{ActiveForm assign="_" id="update-form" action=['user/edit-profile']}}
      {{$_->field($form, 'name')}}

      <label for="profileform-is_black_out_others">{{'Black out other players from the result image'|translate:'app'|escape}}</label>
      {{$_->field($form, 'is_black_out_others')->checkbox()}}

      {{$_->field($form, 'nnid')}}

      {{$_->field($form, 'twitter', [
          'inputTemplate' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-twitter left"></span>@</span>{input}</div>'
        ])}}

      {{$_->field($form, 'ikanakama', [
          'inputTemplate' => '<div class="input-group"><span class="input-group-addon">http://ikazok.net/users/</span>{input}</div>'
        ])}}

      {{$_->field($form, 'env')->textArea([
          'style' => 'height:10em'
        ])->hint(
          Yii::t('app', 'Please tell us about your capture environment and communication between your Wii U and User Agent (e.g. IkaLog). This information will be public.')
        )}}

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
{{/strip}}
