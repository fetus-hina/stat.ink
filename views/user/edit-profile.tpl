{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Update Your Profile'|translate:'app'}}"}}
  {{use class="yii\helpers\Html"}}
  {{use class="yii\bootstrap\ActiveForm" type="block"}}
  {{use class="app\models\User"}}
  <div class="container">
    <h1>
      {{'Update Your Profile'|translate:'app'|escape}}
    </h1>
    
    {{ActiveForm assign="_" id="update-form" action=['user/edit-profile']}}
      {{$_->field($form, 'name')}}

      {{$_options = [
          User::BLACKOUT_NOT_BLACKOUT => Yii::t('app', 'No black out'),
          User::BLACKOUT_NOT_PRIVATE  => Yii::t('app', 'Black out except private battle'),
          User::BLACKOUT_NOT_FRIEND   => Yii::t('app', 'Black out except private battle and teammate on squad battle (tri or quad)'),
          User::BLACKOUT_ALWAYS       => Yii::t('app', 'Black out other players')
        ]}}
      {{$_->field($form, 'blackout')->dropDownList($_options)}}

      <div class="row">
        <div class="col-xs-12 col-sm-11 col-sm-push-1">
          {{include file="_blackout-hint.tpl"}}
          {{registerJs}}
            (function($){
              "use strict";
              $('#profileform-blackout').change(function(){
                updateBlackOutHint($(this).val())
              }).change();
            })(jQuery);
          {{/registerJs}}
        </div>
      </div>

      {{$_->field($form, 'nnid')}}

      {{$_->field($form, 'twitter', [
          'inputTemplate' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-twitter left"></span>@</span>{input}</div>'
        ])->hint(
          Yii::t('app', 'This information will be public. Integration for "log in with twitter" can be done from the profile page.')
        )}}

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
